<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Rival;
use App\Models\ProductMerchant;
use App\Models\PriceHistory;
use App\Models\User;

use App\Api\Kaspi\MerchantGetProductPrice;

class GetRivals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $master_sku;
    protected $shop;
    protected $user;
    protected $product;

    public function __construct($master_sku, Shop $shop)
    {
        $this->master_sku = $master_sku;
        $this->shop = $shop;
        $this->user = User::find($shop->user_id);
        $this->product = Product::where('master_sku', $master_sku)->first();
    }

    public function handle()
    {
        // Получаем данные от API
        $apiData = MerchantGetProductPrice::post($this->master_sku);

        if ($apiData !== null) {
            // Проверяем наличие конкурентов в модели Rival
            $rivals = Rival::all();
            // Проверяем данные и обновляем или создаем новых конкурентов
            if (isset($apiData['offers']) && is_array($apiData['offers'])) {
                foreach ($apiData['offers'] as $offer) {
                    if ($this->shop->shop_id != $offer['merchantId']) {

                        $merchantId = $offer['merchantId'];
                        $merchantName = $offer['merchantName'];
                        $merchantRating = $offer['merchantRating'] ?? 0;
                        $merchantReviewsQuantity = $offer['merchantReviewsQuantity'];
                        $price = $offer['price'];

                        if (isset($offer['delivery'])) {
                            $delivery = $offer['delivery'];
                        } else {
                            $delivery = null;
                        }
                        if (isset($offer['delivery'])) {
                            $deliveryDuration = $offer['deliveryDuration'];
                        } else {
                            $deliveryDuration = null;
                        }

                        $existingRival = $rivals->where('merchantId', $merchantId)->first();

                        if ($existingRival !== null) {
                            // Обновляем существующего конкурента
                            $existingRival->update([
                                'merchantName' => $merchantName,
                                'merchantRating' => $merchantRating,
                                'merchantReviewsQuantity' => $merchantReviewsQuantity,
                            ]);
                        } else {
                            // Создаем нового конкурента
                            $existingRival = Rival::create([
                                'merchantId' => $merchantId,
                                'merchantName' => $merchantName,
                                'merchantRating' => $merchantRating,
                                'merchantReviewsQuantity' => $merchantReviewsQuantity,
                            ]);

                            $this->user->notify(
                                NovaNotification::make()
                                    ->message('Добавлен новый конкурент ' . $merchantName)
                                    // ->action('Открыть', route('platform.product.view', $this->product->id))
                                    ->icon('briefcase')
                                    ->type('info')
                            );
                        }

                        $productMerchantNew = ProductMerchant::where('product_id', $this->product->id)->where('rival_id', $existingRival->id)->first();
                        if ($productMerchantNew == null) {
                            ProductMerchant::create([
                                'product_id' => $this->product->id,
                                'rival_id' => $existingRival->id,
                                'price' => $price,
                                'delivery_at' => $delivery,
                                'deliveryDuration' => $deliveryDuration,
                            ]);

                            $this->user->notify(
                                NovaNotification::make()
                                    ->message('Новый конкурент у товара: ' . $this->product->name . ' | Конкурент: ' . $merchantName)
                                    ->action('Открыть', URL::remote('/app/resources/products/' . $this->product->id))
                                    ->icon('chat')
                                    ->type('info')
                            );
                        }
                    }
                }
            }

            // Проверяем конкурентов товара в модели ProductMerchant и обновляем цены и историю цен
            $productMerchants = ProductMerchant::where('product_id', $this->product->id)->get();

            foreach ($productMerchants as $productMerchant) {
                $merchantId = $productMerchant->rival->merchantId;

                $apiOffer = collect($apiData['offers'])->where('merchantId', $merchantId)->first();

                if ($apiOffer !== null) {
                    $newPrice = $apiOffer['price'];
                    $deliveryAt = $apiOffer['delivery'] ?? null;

                    // Проверяем, изменилась ли цена конкурента
                    if ($productMerchant->price != $newPrice) {
                        // Обновляем цену конкурента

                        $this->user->notify(
                            NovaNotification::make()
                                ->message('Обновлена цена у конкурента: ' . $merchantName .  ' | Конкурент: ' . $this->product->name . ' | Цена: ' . $productMerchant->price)
                                ->action('Открыть продукт', URL::remote('/app/resources/products/' . $this->product->id))
                                ->icon('briefcase')
                                ->type('info')
                        );

                        $productMerchant->update(['price' => $newPrice, 'delivery_at' => $deliveryAt,]);

                        // Добавляем запись в PriceHistory
                        PriceHistory::create([
                            'product_id' => $this->product->id,
                            'price' => $newPrice,
                            'rival_id' => $productMerchant->rival->id,
                            'comment' => 'Изменилась у конкурента',
                        ]);
                    }
                } else {
                    // Конкурент не найден, удаляем его из товара
                    $this->user->notify(
                        NovaNotification::make()
                            ->message('Удален конкурент у товара ' . $this->product->name . ' - ' . $productMerchant->rival->merchantName)
                            ->action('Открыть', URL::remote('/app/resources/products/' . $this->product->id))
                            ->icon('briefcase')
                            ->type('info')
                    );

                    $productMerchant->delete();
                }
            }
        } else {
            Log::error('Ошибка получения данных от API.');
        }
    }
}

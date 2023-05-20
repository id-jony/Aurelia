<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

use App\Models\KaspiSetting;
use App\Models\Rival;
use App\Models\Product;
use App\Models\ProductMerchant;
use App\Models\PriceHistory;
use App\Models\User;

use App\Api\Kaspi\MerchantGetProductPrice;
use App\Notifications\KaspiInfo;

class GetRivals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sku;
    private $product;
    private $user;
    private $setting;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sku, KaspiSetting $setting , Product $product, User $user, Request $request)
    {
        $this->sku = $sku;
        $this->product = $product;
        $this->user = $user;
        $this->setting = $setting;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Проверяем прайс товара у конкурентов
            $price = MerchantGetProductPrice::gen($this->sku, '750000000');
            foreach (json_decode($price)->offers as $offer) {
                if ($this->setting->shop_id != $offer->merchantId) {

                  

                    // Проверяем конкурента в базе
                    $rival = Rival::where('merchantId', $offer->merchantId)->first();
                    if ($rival === null) {
                        $rival = new Rival();
                        $rival->merchantId = $offer->merchantId;
                        $rival->merchantName = $offer->merchantName;
                        $rival->merchantRating = $offer->merchantRating ?? 0;
                        $rival->merchantReviewsQuantity = $offer->merchantReviewsQuantity ?? '0';
                        $this->user->notify(new KaspiInfo('Добавлен новый конкурент ', $rival->merchantName, '#'));
                    } elseif ($rival->merchantName != $offer->merchantName || $rival->merchantRating ?? 0 != $offer->merchantRating) {
                        $rival->merchantName = $offer->merchantName;
                        $rival->merchantRating = $offer->merchantRating;
                        $rival->merchantReviewsQuantity = $offer->merchantReviewsQuantity ?? '0';
                        // $this->user->notify(new KaspiInfo('Обновлен конкурент ', $rival->merchantName, '#'));
                    }
                    $rival->save();
                    // -------------------------------------

                    // Проверяем конкурента товара в базе
                    $product_merchant = ProductMerchant::where('rival_id', $rival->id)->where('product_id', $this->product->id)->first();
                    if ($product_merchant === null) {
                        $product_merchant = new ProductMerchant();
                        $product_merchant->rival_id = $rival->id;
                        $product_merchant->product_id = $this->product->id;
                        $product_merchant->price = $offer->price;
                        $product_merchant->delivery_at = $offer->delivery;
                        $product_merchant->deliveryDuration = $offer->deliveryDuration;

                        $this->user->notify(new KaspiInfo('Новый конкурент у товара ', $this->product->name . ' - ' . $rival->merchantName, route('platform.product.view', $this->product->id)));
                    } elseif ($product_merchant->price != $offer->price) {
                        $price_history = new PriceHistory();
                        $price_history->product_id = $product_merchant->product_id;
                        $price_history->price = $product_merchant->price;
                        $price_history->rival_id = $product_merchant->rival_id;
                        $price_history->comment = 'Изменилась у конкурента';
                        $price_history->save();

                        $product_merchant->price = $offer->price;
                        $product_merchant->delivery_at = $offer->delivery;
                        $product_merchant->deliveryDuration = $offer->deliveryDuration;
                        $this->user->notify(new KaspiInfo('Обновлена цена у конкурента', $this->product->name . ' - ' . $rival->merchantName .''. $price_history->price, route('platform.product.view', $this->product->id)));
                    } elseif ($product_merchant->deliveryDuration != $offer->deliveryDuration) {
                        $product_merchant->delivery_at = $offer->delivery;
                        $product_merchant->deliveryDuration = $offer->deliveryDuration;
                        // $usern->notify(new KaspiInfo('Обновлено воемя доставки конкурента у товара ', $product->name . ' - ' . $rival->merchantName, route('platform.product.view', $product->id)));
                    }
                    $product_merchant->save();
                    // -------------------------------------
                }
            }
    }
}

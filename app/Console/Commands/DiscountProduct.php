<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rival;
use App\Models\User;
use App\Models\PriceHistory;
use App\Models\ProductMerchant;
use App\Models\PriceManagement;
use App\Models\Discount;

use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\MerchantGetProduct;
use App\Api\Kaspi\UpdateProductPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class DiscountProduct extends Command
{
    protected $signature = 'discount:product {--user=}';

    protected $description = 'Get KaspiApi Orders whith status';

    private array $config = [];


    public function __construct()
    {
        parent::__construct();
        $this->config = config('services.kaspi');
    }

    public function handle()
    {
        $this->info('Старт обновления цены продукта');
        $user = User::find($this->option('user'));
        $city = '750000000';
        $shop = Shop::where('user_id', $user->id)->first();
        $SessionToken = MerchantLogin::gen($shop->username, $shop->password);


        $discounts = Discount::with('products')->where('shop_id', $shop->id)->where('status', 'ACTIVE')->get();

        foreach ($discounts as $discount) {
            $products = $discount->products;

            // Получаем массив идентификаторов продуктов
            $productIds = $products->pluck('id')->toArray();

            // Проверяем, активна ли скидка на текущую дату
            $currentDate = date('Y-m-d');
            $startDate = $discount->start_date->format('Y-m-d');
            $finishDate = $discount->finish_date->format('Y-m-d');

            if ($currentDate >= $startDate && $currentDate <= $finishDate) {
                // Проверяем, была ли уже изменена цена продукта по данной скидке
                $products->each(function ($product) use ($discount, $SessionToken, $city, $shop) {
                    if (!isset($product->price_old)) {
                        // Устанавливаем старую цену
                        $product->price_old = $product->priceBase;
                        // Выключаем автоснижение цены
                        $product->autoreduction = 0;
                        // Рассчитываем цену с учетом скидки в процентах и обновляем цену продукта 
                        $product->priceBase = $product->priceBase - ($product->priceBase * ($discount->value / 100));
                        $product->save();

                        UpdateProductPrice::gen($SessionToken, $city, $product->priceBase, $shop->points, $product->productName, $product->sku);

                        $price_history = new PriceHistory();
                        $price_history->product_id = $product->id;
                        $price_history->price = $product->priceBase;
                        $price_history->user_id = 0;
                        $price_history->comment = 'Установлена скидка: ' . $discount->name;
                        $price_history->save();
                    }
                });
            } else {
                    $discount->status = 'ARCHIVE';
                    $discount->save();
                    // Массовое обновление цен продуктов и сброс поля price_old
                    // $product = Product::whereIn('id', $productIds)->update(['priceBase' => DB::raw('price_old'), 'price_old' => null]);

                    $products->each(function ($product) use ($discount, $SessionToken, $city, $shop) {

                        $product->priceBase = $product->price_old;
                        $product->price_old = null;
                        $product->save();

                        UpdateProductPrice::gen($SessionToken, $city, $product->priceBase, $shop->points, $product->productName, $product->sku);

                        $price_history = new PriceHistory();
                        $price_history->product_id = $product->id;
                        $price_history->price = $product->priceBase;
                        $price_history->user_id = 0;
                        $price_history->comment = 'Закончилась скидка: ' . $discount->name;
                        $price_history->save();
                    });
                
            }
        }

        $this->info('Конец обновления цены продукта');
    }
}

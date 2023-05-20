<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\KaspiSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rival;
use App\Models\User;
use App\Models\PriceHistory;
use App\Models\ProductMerchant;

use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\MerchantGetProduct;
use App\Api\Kaspi\UpdateProductPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Notifications\KaspiInfo;

class UpdateProduct extends Command
{
    protected $signature = 'update:product {--user=}';

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
        $setting = KaspiSetting::where('user_id', $user->id)->first();
        $SessionToken = MerchantLogin::gen($setting->username, $setting->password);

        // Проверяем список товаров на обновлении
        $offerStatus = 'PROCESSING';
        $upd_products = MerchantGetProduct::gen($SessionToken, $offerStatus);
        $collect_sku = collect();
        foreach ($upd_products->offers as $upd_product) {
            $collect_sku->push($upd_product->masterProduct->sku);
        }

        // Проверяем цены товара, изменение отправляем в каспи
        $products = Product::where('user_id', '=', $user->id)->where('offerStatus', 'ACTIVE')->whereNotIn('master_sku', $collect_sku)->get();
        foreach ($products as $product) {
            if ($product->priceBase != $product->priceMin) {
                $product_merchant = ProductMerchant::where('product_id', $product->id)->first();
                if ($product_merchant) {
                    if ($product->priceBase > $product_merchant->price) {
                        if ($product->priceMin < $product_merchant->price) {
                            $price = $product_merchant->price - $setting->interval_demp;

                            $update_price = UpdateProductPrice::gen($SessionToken, $city, $price, $setting->points, $product->productName, $product->sku);

                            $price_history = new PriceHistory();
                            $price_history->product_id = $product->id;
                            $price_history->price = $price;
                            $price_history->user_id = 0;
                            $price_history->comment = 'Автоматический демпинг';
                            $price_history->save();

                            $product->priceBase = $price;
                            $product->save();

                            $user->notify(new KaspiInfo('Обновлена цена товара', 'Старая цена: ' . $product->priceBase . ' Цена конкурента: ' . $price_history->price . ' Новая цена: ' . $price, $product->name, route('platform.product.view', $product->id)));
                        }
                    }
                }
            }
        }
    }
}

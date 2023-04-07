<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\KaspiSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rival;

use App\Helpers\Kaspi\MerchantLogin;
use App\Helpers\Kaspi\MerchantGetProduct;
use App\Helpers\Kaspi\MerchantGetProductPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

class GetProductKaspiMc extends Command
{
    protected $signature = 'get-product:kaspi {--user=}';

    protected $description = 'Get KaspiApi Orders whith status';

    private array $config = [];


    public function __construct()
    {
        parent::__construct();
        $this->config = config('services.kaspi');
    }


    public function handle()
    {
        $this->info('Старт импорт продуктов Kaspi');

        $user = $this->option('user');
        $setting = KaspiSetting::where('user_id', $user)->first();
        $SessionToken = MerchantLogin::gen($setting->username, $setting->password);


        $products = MerchantGetProduct::gen($SessionToken);

        foreach (json_decode($products)->offers as $data) {
            $category = Category::where('code', $data->category->code)->first();

            if ($category === null) {
                $this->info('Найдена новая категория');
                $category = Category::create([
                    'name' => $data->category->name ?? '',
                    'code' => $data->category->code ?? '',
                    'restricted' => $data->category->restricted ?? '0',
                    'closed' => $data->category->closed ?? '0',
                ]);
            }

            $product = Product::where('sku', $data->masterProduct->sku)->first();
            if ($product === null) {
                $this->info('Найден новый продукт');
                $price = MerchantGetProductPrice::gen($data->masterProduct->sku);
                $merchants_price = collect();

                foreach (json_decode($price)->offers as $offer) {
                    if ($setting->shop_id != $offer->merchantId) {
                        $rival = Rival::where('merchantId', $offer->merchantId)->first();
                        if ($rival === null) {
                            $this->info('Найден новый конкурент');
                            $rival = Rival::create([
                                    'merchantId' => $offer->merchantId,
                                    'merchantName' => $offer->merchantName,
                                    'merchantRating' => $offer->merchantRating,
                                    'merchantReviewsQuantity' => $offer->merchantReviewsQuantity,
                            ]);
                        }

                        $merchant = array(
                            "rival_id" => $rival->id,
                            "price" => $offer->price,
                            "delivery" => $offer->delivery,
                            "deliveryDuration" => $offer->deliveryDuration
                        );
                        $merchants_price->push($merchant);
                    }
                }


                $product = Product::create([
                    'sku' => $data->masterProduct->sku ?? '',
                    'name' => $data->masterProduct->name ?? '',
                    'category' => $category->id ?? '',
                    'brand' => $data->masterProduct->brand ?? '',
                    'brandCode' => $data->masterProduct->brandCode ?? '',
                    'brandRestricted' => $data->masterProduct->brandRestricted ?? '0',
                    'brandClosed' => $data->masterProduct->brandClosed ?? '0',
                    'primaryImage' => $data->masterProduct->primaryImage->small ?? '',
                    'productUrl' => $data->masterProduct->productUrl ?? '',
                    'priceBase' => $data->priceMin ?? '',
                    'expireDate' => date("Y-m-d H:i:s", $data->expireDate / 1000),
                    'offerStatus' => $data->offerStatus,
                    'merchants' => $merchants_price,

                ]);
            }
        }




        $this->info('Импорт товаров завершен');
    }
}

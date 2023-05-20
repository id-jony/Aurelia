<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

use App\Models\KaspiSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMerchant;
use App\Models\PriceHistory;
use App\Models\Rival;
use App\Models\Review;
use App\Models\User;

use App\Jobs\GetReviews;
use App\Jobs\GetRivals;

use App\Api\Kaspi\MerchantLogin;
use App\Api\Kaspi\MerchantGetProduct;
use App\Api\Kaspi\MerchantGetProductPrice;
use App\Api\Kaspi\GetProductReviews;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

use App\Notifications\KaspiInfo;

class GetProductKaspiMc extends Command
{
    protected $signature = 'get-product:kaspi {--user=}';
    protected $description = 'Get KaspiApi Product';


    public function handle(Request $request)
    {
        $this->info('Старт импорт товаров Kaspi');

        $user = $this->option('user');
        $usern = User::find($user);
        $offerStatus = null;
        $setting = KaspiSetting::where('user_id', $user)->first();
        $SessionToken = MerchantLogin::gen($setting->username, $setting->password);
        $products = MerchantGetProduct::gen($SessionToken, $offerStatus);

        foreach ($products->offers as $data) {
            // Проверяем категорию товара
            if ($data->category != null) {
                $category = Category::where('code', $data->category->code)->first();
                if ($category === null) {
                    $category = new Category();
                    $category->name = $data->category->name ?? '';
                    $category->code = $data->category->code ?? '';
                    $category->restricted = $data->category->restricted ?? '0';
                    $category->closed = $data->category->closed ?? '0';

                    $usern->notify(new KaspiInfo('Добавлена новая категория товаров ', $category->name, '#'));
                } elseif ($category->name != $data->category->name || $category->restricted != $data->category->restricted || $category->closed != $data->category->closed) {
                    $category->name = $data->category->name ?? '';
                    $category->restricted = $data->category->restricted ?? '0';
                    $category->closed = $data->category->closed ?? '0';

                    $usern->notify(new KaspiInfo('Категория обновлена ', $category->name, '#'));
                }
                $category->save();
            }
            // ---------------------------

            // Проверяем товар
            $product = Product::where('master_sku', $data->masterProduct->sku)->first();
            if ($product === null) {
                $product = new Product();
                $product->sku = $data->sku;
                $product->master_sku = $data->masterProduct->sku ?? '';
                $product->name = $data->masterProduct->name ?? '';
                $product->productName = $data->name ?? '';
                $product->category = $category->id ?? '';
                $product->brand = $data->masterProduct->brand ?? '';
                $product->brandCode = $data->masterProduct->brandCode ?? '';
                $product->brandRestricted = $data->masterProduct->brandRestricted ?? '0';
                $product->brandClosed = $data->masterProduct->brandClosed ?? '0';
                $product->primaryImage = $data->masterProduct->primaryImage->small ?? '';
                $product->productUrl = $data->masterProduct->productUrl ?? '';
                $product->priceBase = $data->priceMin ?? '';
                $product->expireDate = date("Y-m-d H:i:s", $data->expireDate / 1000);
                $product->offerStatus = $data->offerStatus;
                $product->user_id = $user;
                $usern->notify(new KaspiInfo('Найден новый товар ', $product->name, route('platform.product.view', $product->id)));
            } elseif ($product->offerStatus != $data->offerStatus || $product->priceBase != $data->priceMin || $product->productUrl != $data->masterProduct->productUrl || $product->primaryImage != $data->masterProduct->primaryImage->small || $product->brandRestricted != $data->masterProduct->brandRestricted || $product->brandClosed != $data->masterProduct->brandClosed) {
                $product->brandRestricted = $data->masterProduct->brandRestricted ?? '0';
                $product->brandClosed = $data->masterProduct->brandClosed ?? '0';
                $product->primaryImage = $data->masterProduct->primaryImage->small ?? '';
                $product->productUrl = $data->masterProduct->productUrl ?? '';
                $product->priceBase = $data->priceMin ?? '';
                $product->expireDate = date("Y-m-d H:i:s", $data->expireDate / 1000);
                $product->offerStatus = $data->offerStatus;
                $usern->notify(new KaspiInfo('Товар обновлен ', $product->name, route('platform.product.view', $product->id)));
            }
            $product->updated_at = now();
            $product->save();
            // ------------------------------------

            // Проверяем прайс товара у конкурентов
            GetRivals::dispatch($data->masterProduct->sku, $setting, $product, $usern, $request);

            // Проверяем отзывы товара
            GetReviews::dispatch($data->masterProduct->sku, $product, $usern, $request);

        }
        $this->info('Импорт товаров завершен');
    }
}

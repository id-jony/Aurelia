<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMerchant;
use App\Models\PriceHistory;
use App\Models\Rival;
use App\Models\Review;
use App\Models\User;

use App\Jobs\GetReviews;
use App\Jobs\GetRivals;
use App\Jobs\GetPromo;
use App\Jobs\GetPosition;

use App\Api\Kaspi\GetProductPromo;
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

use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class GetProductKaspiMc extends Command
{
    protected $signature = 'kaspi:product {--user=}';
    protected $description = 'Get KaspiApi Product';


    public function handle(Request $request)
    {
        $this->info('Старт импорт товаров Kaspi');

        $user = $this->option('user');
        $usern = User::find($user);
        $offerStatus = null;
        $shop = Shop::where('user_id', $user)->first();
        $SessionToken = MerchantLogin::gen($shop->username, $shop->password);
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

                    $usern->notify(
                        NovaNotification::make()
                            ->message('Добавлена новая категория товаров: ' . $category->name)
                            // ->action('Открыть', route('platform.product.view', $product->id))
                            ->icon('presentation-chart-line')
                            ->type('info')
                    );
                } elseif ($category->name != $data->category->name || $category->restricted != $data->category->restricted || $category->closed != $data->category->closed) {
                    $category->name = $data->category->name ?? '';
                    $category->restricted = $data->category->restricted ?? '0';
                    $category->closed = $data->category->closed ?? '0';

                    $usern->notify(
                        NovaNotification::make()
                            ->message('Категория обновлена: ' . $category->name)
                            // ->action('Открыть', route('platform.product.view', $product->id))
                            ->icon('presentation-chart-line')
                            ->type('info')
                    );
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
                $product->category_id = $category->id ?? '';
                $product->brand = $data->masterProduct->brand ?? '';
                $product->brandCode = $data->masterProduct->brandCode ?? '';
                $product->brandRestricted = $data->masterProduct->brandRestricted ?? '0';
                $product->brandClosed = $data->masterProduct->brandClosed ?? '0';
                $product->primaryImage = $data->masterProduct->primaryImage->large ?? '';
                $product->productUrl = $data->masterProduct->productUrl ?? '';
                $product->priceBase = $data->priceMin ?? '';
                $product->expireDate = date("Y-m-d H:i:s", $data->expireDate / 1000);
                $product->offerStatus = $data->offerStatus;
                $product->user_id = $user;

                $usern->notify(
                    NovaNotification::make()
                        ->message('Найден новый товар ' . $product->name)
                        ->action('Открыть', URL::remote('/app/resources/products/' . $product->id))
                        ->icon('shopping-bag')
                        ->type('info')
                );
            } elseif ($product->offerStatus != $data->offerStatus || $product->priceBase != $data->priceMin || $product->productUrl != $data->masterProduct->productUrl || $product->brandRestricted != $data->masterProduct->brandRestricted || $product->brandClosed != $data->masterProduct->brandClosed) {
                $product->offerStatus = $data->offerStatus;
                if ($product->offerStatus != 'ARCHIVE') {
                    $product->brandRestricted = $data->masterProduct->brandRestricted ?? '0';
                    $product->brandClosed = $data->masterProduct->brandClosed ?? '0';
                    $product->primaryImage = $data->masterProduct->primaryImage->large ?? '';
                    $product->productUrl = $data->masterProduct->productUrl ?? '';
                    $product->priceBase = $data->priceMin ?? '';
                    $product->expireDate = date("Y-m-d H:i:s", $data->expireDate / 1000);

                    $usern->notify(
                        NovaNotification::make()
                            ->message('Товар обновлен ' . $product->name)
                            ->action('Открыть', URL::remote('/app/resources/products/' . $product->id))
                            ->icon('shopping-bag')
                            ->type('info')
                    );
                }
            }
            $product->updated_at = now();
            $product->save();
            // ------------------------------------

            if ($product->offerStatus != 'ARCHIVE') {
                // Проверяем прайс товара у конкурентов
                GetRivals::dispatch($data->masterProduct->sku, $shop);
                // Проверяем отзывы товара
                GetReviews::dispatch($data->masterProduct->sku, $product, $usern, $request);

                // Проверяем позицию товара
                // GetPosition::dispatch($product);

            }
        }


        $this->info('Импорт товаров завершен');
    }
}

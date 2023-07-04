<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\Models\Product;
use App\Api\Kaspi\GetProductPromo;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class GetPromo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sku;

    public $failOnTimeout = false;
    public $timeout = 120000;

    /**
     * Создать новый экземпляр задачи.
     *
     * @param  string  $sku
     * @return void
     */
    public function __construct(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * Выполнить задачу.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $promoData = $this->fetchProductPromoData();
                foreach ($promoData['data'] as $promo) {
                    $product = $this->findProductByMasterSku($promo['id']);
                    $promoItems = $this->extractPromosFromData($promo['promo']);
                    $this->updateProductPromo($product, $promoItems);
                }
            
            
        } catch (\Exception $e) {
            Log::error('Произошла ошибка при выполнении задачи GetPromo для sku: ' . $this->sku . ', Ошибка: ' . $e->getMessage());
        }
    }

    /**
     * Получить данные о промо-акциях товара.
     *
     * @return array
     */
    private function fetchProductPromoData(): array
    {
        return GetProductPromo::gen($this->sku);
    }

    /**
     * Найти товар по мастер-SKU.
     *
     * @param  string  $masterSku
     * @return Product|null
     */
    private function findProductByMasterSku(string $masterSku): ?Product
    {
        return Product::where('master_sku', $masterSku)->first();
    }

    /**
     * Извлечь промо-акции из данных о промо.
     *
     * @param  array  $promoData
     * @return array
     */
    private function extractPromosFromData(array $promoData): array
    {
        $promoItems = [];

        foreach ($promoData as $promo) {
            $promoInfo = [
                'code' => $promo['code'],
                'type' => $promo['type'],
                'src' => $promo['img']['large']['src']
            ];
            $promoItems[] = $promoInfo;
        }

        return $promoItems;
    }

    /**
     * Обновить информацию о промо-акциях товара.
     *
     * @param  Product  $product
     * @param  array  $promoItems
     * @return void
     */
    private function updateProductPromo(Product $product, array $promoItems)
    {
        $product->promo = $promoItems;
        $product->save();
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\GetPromo;
use App\Models\Product;

class KaspiPromo extends Command
{
    protected $signature = 'kaspi:promo {--user=}';
    protected $description = 'Получение данных о промо акциях товаров на kaspi';

    public function handle()
    {
        // Получение модели продуктов
        $products = Product::where('user_id', $this->option('user'))->where('offerStatus', 'ACTIVE');
 
        // Извлечение всех значений столбца master_sku
        $masterSkus = $products->pluck('master_sku')->toArray();

        // Разбиваем массив на части по 9 элементов
        $chunks = array_chunk($masterSkus, 9);

        // Обрабатываем каждую часть
        foreach ($chunks as $chunk) {
            // Объединение значений в одну строку
            $result = implode(', ', $chunk);

            // Проверяем акции товара
            GetPromo::dispatch($result);
        }
    }
}

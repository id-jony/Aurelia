<?php

namespace App\Nova\Cards;

use Abordage\TableCard\TableCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Product;
use \App\Models\ProductMerchant;

class TopRival extends TableCard
{
    /**
     * Name of the card (optional, remove if not needed)
     */
    public string $title = 'Топ конкурентов';

    /**
     * The width of the card (1/2, 1/3, 1/4 or full).
     */
    public $width = '1/3';

    /**
     * Array of table rows
     *
     * Required keys: title, viewUrl
     * Optional keys: subtitle, editUrl
     */
    public function rows(): array
    {
        $rows = [];

        $products = Product::where('user_id', Auth::user()->id)->select('id')->where('offerStatus', 'ACTIVE')
            ->get()->toArray();

        /** for example */
        $merchants = ProductMerchant::whereIn('product_id', $products)->select('rival_id', DB::raw('COUNT(*) as total'))
            ->groupBy('rival_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();



        foreach ($merchants as $model) {
            $rows[] = [
                'title' => $model->rival->merchantName,
                'subtitle' => 'Одинаковых товаров: ' . $model->total,
                // 'viewUrl' => $this->getResourceUrl($model),
            ];
        }

        return $rows;
    }
}

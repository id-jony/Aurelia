<?php

namespace App\Nova\Cards;

use Abordage\TableCard\TableCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Product;
use \App\Models\OrderShipment;

class TopProduct extends TableCard
{
    /**
     * Name of the card (optional, remove if not needed)
     */
    public string $title = 'Топ товаров';

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

        /** for example */
        $products = Product::where('user_id', Auth::user()->id)->where('offerStatus', 'ACTIVE')
            ->get()->sortByDesc('count')->take(3);

       

        foreach ($products as $model) {
            $rows[] = [
                'title' => $model->name,
                'subtitle' => $model->count,
                'viewUrl' => $this->getResourceUrl($model),
            ];
        }

        return $rows;
    }
}

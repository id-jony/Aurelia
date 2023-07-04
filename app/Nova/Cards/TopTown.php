<?php

namespace App\Nova\Cards;

use Abordage\TableCard\TableCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \App\Models\Сonsumer;
use \App\Models\OrderShipment;

class TopTown extends TableCard
{
    /**
     * Name of the card (optional, remove if not needed)
     */
    public string $title = 'Топ городов';

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
        $products = Сonsumer::where('town', '!=', '')->where('user_id', Auth::user()->id)->select('town', DB::raw('COUNT(*) as total'))
            ->groupBy('town')
            ->orderByDesc('total')
            ->take(5)
            ->get();



        foreach ($products as $model) {
            $rows[] = [
                'title' => $model->town,
                'subtitle' => 'Заказов из города: ' . $model->total,
                // 'viewUrl' => $this->getResourceUrl($model),
            ];
        }

        return $rows;
    }
}

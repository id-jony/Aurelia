<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

use App\Nova\Metrics\NewOrders;
use App\Nova\Metrics\MoneyOrders;
use App\Nova\Metrics\AverageOrders;
use App\Nova\Metrics\NetProfit;

use App\Nova\Cards\TopProduct;
use App\Nova\Cards\TopTown;
use App\Nova\Cards\TopRival;

use Coroowicaksono\ChartJsIntegration\StackedChart;
use Illuminate\Support\Facades\Auth;

class Main extends Dashboard
{

    public function name()
    {
        return 'Метрика';
    }


    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [ 
            NewOrders::make()->defaultRange('30')->width('1/4'),
            MoneyOrders::make()->defaultRange('30')->width('1/4'),
            AverageOrders::make()->defaultRange('30')->width('1/4'),
            NetProfit::make()->defaultRange('30')->width('1/4'),

            
            
            (new StackedChart())
                ->title('Статистика продаж')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->model('App\Models\Order')
                ->col_xaxis('creationDate')
                ->options([
                    'legend' => [
                        'display' => false
                    ],
                    'latestData' => 32,
                    'uom' => 'month', // available in 'day', 'week', 'month', 'hour'
                    'startWeek' => '0',
                    'sum' => 'totalPrice',
                    'queryFilter' => array([    // add array of filter with this format
                        'key' => 'user_id',
                        'operator' => '=',
                        'value' => Auth::user()->id
                    ], [    // add array of filter with this format
                        'key' => 'status',
                        'operator' => '!=',
                        'value' => 'CANCELLED'
                    ])
                ])
                ->width('2/3'),
                TopProduct::make(),
                TopTown::make(),
                TopRival::make(),

        ];
    }
}

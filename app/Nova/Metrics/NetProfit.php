<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use App\Models\Order;
use App\Models\Shop;

use Illuminate\Support\Facades\DB;

class NetProfit extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user->id)->where('status', '!=', 'CANCELLED');
        $collect = collect();

        foreach ($orders as $order) {
            $collect->push([
                'totalPrice' => $order->totalPrice,
                'creationDate' => $order->creationDate
            ]);
        }

        return $this->sum($request, $orders, 'totalPrice', 'creationDate')->format('0,0')->currency('₸');

    }

    public function name()
    {
        return 'Чистая прибыль';
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            'TODAY' => Nova::__('Сегодня'),
            30 => Nova::__('30 Дней'),
            60 => Nova::__('60 Дней'),
            365 => Nova::__('365 Дней'),
            'MTD' => Nova::__('С начала месяца'),
            'QTD' => Nova::__('За квартал'),
            'YTD' => Nova::__('За год'),
        ];
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }
}

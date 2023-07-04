<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\URL;

use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;

use App\Nova\Metrics\NewOrders;
use App\Nova\Metrics\MoneyOrders;
use App\Nova\Metrics\AverageOrders;


class Order extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Order::class;
    public static $title = 'code';

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', $request->user()->id);
    }
    
    public static function label()
    {
        return 'Заказы';
    }

    public static function singularLabel()
    {
        return 'Заказ';
    }

    public static function searchable()
    {
        return false;
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return $request->user()->isSuperAdmin();
    }

    public static $search = [
        'id',
        'kaspi_id',
        'code',
        'totalPrice',
        'paymentMode',
        'deliveryCostForSeller',
        'isKaspiDelivery',
        'signatureRequired',
        'deliveryMode',
        'creditTerm',
        'waybill',
        'state',
        'status',
        'customer_id',
        'preOrder',
        'pickupPointId',
        'deliveryAddress',
        'deliveryCost',
        'creationDate',
        'transmissionDate',
        'plannedDeliveryDate',
        'user_id'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            // ID::make()->sortable(),

            Text::make('Статус', function ($model) {
                foreach ($model::STATUS_VALUE as $key => $value) {
                    if ($key == $model->status) {
                        return $value;
                    }
                }
            })->sortable(),

            Date::make('Дата поступления заказа', 'creationDate')->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Планируемая дата доставки заказа', 'plannedDeliveryDate')->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            URL::make('Накладная', fn () => $this->waybill),

            Text::make('Способ оплаты', function ($model) {
                foreach ($model::PAYMENT_VALUE as $key => $value) {
                    if ($key == $model->paymentMode) {
                        return $value;
                    }
                }
            })->sortable(),

            Text::make('Способ доставки', function ($model) {
                foreach ($model::DELIVERY_VALUE as $key => $value) {
                    if ($key == $model->deliveryMode) {
                        if ($model->isKaspiDelivery === 1 && $model->deliveryMode === 'DELIVERY_PICKUP') {
                            return 'Kaspi постомат';
                        } else {
                            return $value;
                        }
                    }
                }
            })->sortable(),

            Text::make('Адрес доставки', 'deliveryAddress'),

            Currency::make('Общая стоимость', 'totalPrice')->currency('KZT'),

            Stack::make('Покупатель', [
                BelongsTo::make('', 'customer', Сonsumer::class),
                Line::make('customer')->asSmall()->resolveUsing(function () {
                    return $this->customer->phone;
                }),
            ]),


            Date::make('Обновлено', 'updated_at')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),


            Tabs::make('Some Title', [

                HasMany::make('Товары', 'products', OrderShipment::class),
            ]),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [
            NewOrders::make()->defaultRange('30'),
            MoneyOrders::make()->defaultRange('30'),
            AverageOrders::make()->defaultRange('30'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make()->withFormat(function ($model) {
                return [
                    'ID' => $model->getKey(),
                    'protocol' => $model->protocol,
                    'ip' => $model->ip,
                    'status' => $model->status,
                    'port' => $model->port,
                ];
            }),
        ];
    }
}

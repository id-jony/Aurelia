<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;


use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Pragma\ProductTitle\ProductTitle;

class PriceManagement extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\PriceManagement::class;
    public static $title = 'product_id';

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', $request->user()->id);
    }
    
    public static function label()
    {
        return 'Список товаров';
    }

    public static function singularLabel()
    {
        return 'Товар';
    }

    public static function searchable()
    {
        return false;
    }

    public static function authorizedToCreate(Request $request)
    {
        return true;
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
        'user_id',
        'product_id',
        'keep_published',
        'autoreduction',
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

            ProductTitle::make('Товар')->resolveUsing(function ($value, $resource) {
                return [
                    'primaryImage' => $resource->product->primaryImage,
                    'name' => $resource->product->name,
                    'sku' => $resource->product->sku,
                    'brand' => $resource->product->brand,
                    'category' => $resource->product->categories->name,
                    'status' => $resource->product->status,

                ];
            })->hideWhenCreating()->hideWhenUpdating(),

            Currency::make('Актуальная цена', 'product.priceBase')->currency('KZT'),
            // Currency::make('Минимальная цена', 'priceMin')->currency('KZT'),
            // Currency::make('Себестоимость', 'price_cost')->currency('KZT'),
  
            // BelongsTo::make('Пользователь', 'user', User::class),

            Boolean::make('Всегда держать опубликованным', 'keep_published')->sortable(),
            Boolean::make('Вкл. автоснижение', 'autoreduction')->sortable(),

            Date::make('Обновлено', 'updated_at')->hideWhenCreating()->hideWhenUpdating()
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),



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
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            
        ];
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

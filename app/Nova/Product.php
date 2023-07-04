<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Stack;
use \Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\Number;
use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;

use Pragma\ProductTitle\ProductTitle;
use Pragma\ProductPrice\ProductPrice;

use WesselPerik\StatusField\StatusField;

class Product extends Resource
{

    use HasTabs;

    public static $displayInNavigation = true;
    // public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Product::class;
    public static $title = 'name';

    public static function indexQuery(NovaRequest $request, $query)
    {

        return $query->where('user_id', $request->user()->id)->when(empty($request->get('offerStatus')), function(Builder $query) {
            $query->getQuery()->orders = [];
            return $query->orderBy('offerStatus', 'asc');
        });
    }
    

    public static function label()
    {
        return 'Товары';
    }

    public static function singularLabel()
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
    
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return $request->user()->isSuperAdmin();
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
    
    public static $search = [
        'id',
        'sku',
        'master_sku',
        'name',
        'brand',
        'brandCode',
        'brandRestricted',
        'brandClosed',
        'primaryImage',
        'productUrl',
        'priceMin',
        'priceBase',
        'expireDate',
        'offerStatus',
        'productName',
        'user_id',
        'price_cost'
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
            // StatusField::make('Статус', 'offerStatus')
            //     ->icons([
            //         'minus-circle' => $this->offerStatus == 'ARCHIVE',
            //         // 'clock'        => $this->offerStatus == 1 && $this->offerStatus == 0,
            //         'check-circle' => $this->offerStatus == 'ACTIVE'
            //     ])
            //     ->color([
            //         'minus-circle' => 'red-500',
            //         'clock'        => 'blue-500',
            //         'check-circle' => 'green-500'
            //     ])
            //     ->solid(false) // optional
            //     // ->tooltip($this->status)
            //     // ->info($this->status) // optional
            //     ->exceptOnForms()
            //     ->onlyOnIndex()
            //     ->sortable(),

            ProductTitle::make('Товар')->resolveUsing(function ($value, $resource) {
                return [
                    'primaryImage' => $resource->primaryImage,
                    'name' => $resource->name,
                    'sku' => $resource->sku,
                    'rivalcount' => $resource->rivalcount,
                    'category' => $resource->categories->name,
                    'status' => $resource->status,
                    'productUrl' => $resource->productUrl,
                    'promo' => $resource->promo,
                    'priceBase' => $resource->priceBase,
                    'SumMoney' => $resource->SumMoney,
                    'Count' => $resource->Count,
                    'priceMin' => $resource->priceMin,
                    'price_old' => $resource->price_old,
                    'price_cost' => $resource->price_cost,
                    'product' => $resource,
                ];
            }),

            // Number::make('Конкуренты', 'rivalcount')->onlyOnIndex(),

            // Image::make('Фото')->disk('public')
            //     ->disableDownload()
            //     ->thumbnail(function ($value, $disk) {
            //         return $this->primaryImage;
            //     })
            //     ->preview(function ($value, $disk) {
            //         return $this->primaryImage;
            //     })
            //     ->showOnIndex()
            //     ->squared()
            //     ->deletable(false),

            // Text::make('Код товара', 'sku')
            //     ->sortable()
            //     ->rules('required', 'max:255'),

            // Text::make('Код карточки', 'master_sku')
            //     ->sortable()
            //     ->rules('required', 'max:255'),

            // Text::make('Наименование', 'name')
            //     ->sortable()
            //     ->rules('required', 'max:255'),

            // BelongsTo::make('Категория', 'categorys', Category::class),

            // Text::make('Бренд', 'brand')
            //     ->sortable()
            //     ->rules('required', 'max:50'),

            // Boolean::make('Ограниченный', 'brandRestricted')
            //     ->sortable(),

            // Boolean::make('Закрытый', 'brandClosed')
            //     ->sortable(),

            // URL::make('Ссылка kaspi.kz', fn () => $this->productUrl),

            // ProductPrice::make('Стоимость')->resolveUsing(function ($value, $resource) {
            //     return [
            //         'id' => $resource->id,
            //         'priceBase' => $resource->priceBase,
            //         'priceMin' => $resource->priceMin,
            //         'price_old' => $resource->price_old,
            //         'price_cost' => $resource->price_cost,
            //         'commission' => $resource->categories->commission,
            //         'autoreduction' => $resource->autoreduction,
            //         'keep_published' => $resource->keep_published,
            //         'position' => $resource->position,

            //     ];
            // }),

            // Stack::make('Цены(Акт. Мин. Себес.)', [
            //     Currency::make('Актуальная цена', 'priceBase')->currency('KZT'),
            //     Currency::make('Минимальная цена', 'priceMin')->currency('KZT'),
            //     Currency::make('Себестоимость', 'price_cost')->currency('KZT'),
            // ]),



            // Date::make('Обновлено', 'updated_at')
            //     ->sortable()
            //     ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            

            Tabs::make('Some Title', [
                HasMany::make('Конкуренты', 'rivals', ProductMerchant::class),
                HasMany::make('Заказы', 'shipments', OrderShipment::class),
                HasMany::make('Отзывы покупателей', 'reviews', Review::class),
                HasMany::make('История цены', 'prices', PriceHistory::class),

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

<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use ZiffMedia\NovaSelectPlus\SelectPlus;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Shop;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\BelongsToMany;
use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;
use Illuminate\Support\Facades\Auth;

class Discount extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Discount::class;
    public static $title = 'name';

    public static function indexQuery(NovaRequest $request, $query)
    {
        $shop = Shop::where('user_id', Auth::user()->id)->get()->pluck('id');
        return $query->whereIn('shop_id', $shop);
    }

    public static function label()
    {
        return 'Промо-акции';
    }

    public static function singularLabel()
    {
        return false;
    }

    public static function searchable()
    {
        return true;
    }

    public static function authorizedToCreate(Request $request)
    {
        return true;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
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
        'name',
        'type',
        'value',
        'shop_id',
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

            Text::make('Название промо-акции', 'name')
                ->sortable()
                ->rules('required', 'max:50'),
            Select::make('Тип промо-акции', 'type')->options([
                'percent' => 'Скидка на товары в процентах',
                'fix' => 'Фиксированная скидка на товары',
            ])->displayUsingLabels(),

            Select::make('Статус', 'status')->options([
                'ACTIVE' => 'Активный',
                'ARCHIVE' => 'В архиве',
            ])->displayUsingLabels(),

            Select::make('Магазин', 'shop_id')->options(Shop::where('user_id', Auth::user()->id)->get()->pluck('shop_name', 'id'))->displayUsingLabels(),

            Text::make('Значение', 'value')
                ->sortable()
                ->rules('required', 'max:50')
                ->displayUsing(fn ($value) => $value ? $value . '%' : '-'),

            SelectPlus::make('Товары', 'products', Product::class)
                ->label(fn ($state) =>  "<img src=\"{$state->primaryImage}\" width=\"64px\">" . $state->name . " <span class=\"text-xs\">({$state->sku})</span>")
                ->optionsQuery(function (Builder $query) {
                    $query->where('user_id', Auth::user()->id)
                        ->whereNotIn('products.id', function ($Subquery) {
                            $Subquery->select('product_id')
                                ->from('discount_has_products');
                        });;
                }),

            Date::make('Дата начала акции', 'start_date')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Дата окончания акции', 'finish_date')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Обновлено', 'updated_at')
                ->sortable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Создано', 'created_at')
                ->sortable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Tabs::make('Some Title', [
                BelongsToMany::make('Товары', 'products', Product::class),
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

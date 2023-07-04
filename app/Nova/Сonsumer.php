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
use Laravel\Nova\Fields\Boolean;

use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Сonsumer extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Сonsumer::class;
    public static $title = 'name';

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('user_id', $request->user()->id);
    }

    public static function label()
    {
        return 'Покупатели';
    }

    public static function singularLabel()
    {
        return 'Покупатель';
    }

    public static function searchable()
    {
        return false;
    }

    public static function authorizedToCreate(Request $request)
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
        'name',
        'phone',
        'kaspi_id',
        'town',
        'whatsapp',
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
            // ID::make()->sortable()->onlyOnIndex(),

            Text::make('Имя', 'name')
                ->sortable()
                ->rules('required', 'max:50'),

            PhoneNumber::make('Телефон', 'phone')
                ->sortable()
                ->onlyCountries('KZ'),

            Boolean::make('Whatsapp', 'whatsapp')
                ->sortable(),

            Text::make('Город', 'town')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Кол-во заказов', 'ordercount')
                ->sortable()
                ->rules('required', 'max:50'),


            Tabs::make('Some Title', [
                HasMany::make('Заказы', 'orders', Order::class),
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
        return [];
    }
}

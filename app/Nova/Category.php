<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;

use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;

class Category extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Category::class;
    public static $title = 'name';

    public static function label()
    {
        return 'Категории товаров';
    }

    public static function singularLabel()
    {
        return 'Категория';
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
        'name',
        'code',
        'restricted',
        'closed',
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

            Text::make('Название', 'name')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Код', 'code')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Комиссия', 'commission')
                ->sortable()
                ->rules('required', 'max:50')
                ->displayUsing(fn ($value) => $value ? $value . '%' : '-'),


            Text::make('Ограниченная', 'restricted')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Закрытая', 'closed')
                ->sortable()
                ->rules('required', 'max:50'),

            Date::make('Обновлено', 'updated_at')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Создано', 'created_at')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Tabs::make('Some Title', [

                HasMany::make('Товары', 'products', Product::class),
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

<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;

class KaspiSetting extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Shop::class;
    public static $title = 'Открыть настройки';

    public static function label()
    {
        return 'Настройки';
    }

    public static function singularLabel()
    {
        return 'Настройки';
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
        // 'id',
        // 'user_id',
        // 'token',
        // 'username',
        // 'password',
        // 'count_day',
        // 'interval_day',
        // 'shop_name',
        // 'shop_id',
        // 'points',
        // 'percent_demp',
        // 'interval_demp',
        // 'percent_sales'
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

            Text::make('Токен', 'token')
                ->sortable()
                ->rules('required'),

            Text::make('Email', 'username')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Пароль', 'password')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Сколько дней сканировать (рекомендуемое значение 30)', 'count_day')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Интервал дней сканирования (рекомендуемое значение 14)', 'interval_day')
                ->sortable()
                ->rules('required', 'max:50'),
            
            Text::make('Максимальный процент демпинга (%)', 'percent_demp')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Интервал изменения цены (₸)', 'interval_demp')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Комиссия за продажи', 'percent_sales')
                ->sortable()
                ->rules('required', 'max:50'),
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

<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Actions\ExportAsCsv;

class Town extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $model = \App\Models\Town::class;
    public static $title = 'ru';

    public static function label()
    {
        return 'Список городов';
    }

    public static function singularLabel()
    {
        return 'Город';
    }

    public function authorizedToDelete(Request $request)
    {
        return $request->user()->isSuperAdmin();
    }

    public static $search = [
        'id',
        'kaspi_id',
        'ru',
        'kz',
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
            Text::make('Протокол', 'kaspi_id')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('На русском', 'ru')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('На казахском', 'kz')
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

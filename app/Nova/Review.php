<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Date;

use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Fields\BelongsTo;

class Review extends Resource
{

    public static $displayInNavigation = true;
    public static $tableStyle = 'tight';
    public static $showColumnBorders = false;
    public static $perPageViaRelationship = 10;
    public static $model = \App\Models\Review::class;
    public static $title = 'customer_author';

    public static function label()
    {
        return 'Отзывы';
    }

    public static function singularLabel()
    {
        return 'Отзыв';
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
        'customer_id',
        'customer_author',
        'product_id',
        'rating',
        'photo',
        'plus',
        'minus',
        'text',
        'date'
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

            Image::make('Фото')->disk('public')
                ->disableDownload()
                ->thumbnail(function ($value, $disk) {
                    return $this->photo;
                })
                ->preview(function ($value, $disk) {
                    return $this->photo;
                })
                ->showOnIndex()
                ->squared()
                ->deletable(false),

            Text::make('Покупатель', 'customer_author')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsTo::make('Товар', 'product', Product::class),

            Text::make('Рейтинг', 'rating')
                ->sortable()
                ->rules('required', 'max:50'),

            Text::make('Отзыв', 'Review')
                ->sortable()
                ->rules('required', 'max:50'),

            Date::make('Дата отзыва', 'date')
                ->sortable()
                ->displayUsing(fn ($value) => $value ? $value->format('d-m-Y H:i:s') : ''),

            Date::make('Обновлено', 'updated_at')
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

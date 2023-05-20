<?php

namespace App\Orchid\Layouts\Product;

use Orchid\Screen\Layouts\Chart;

class ProductOrderChart extends Chart
{
    /**
     * Add a title to the Chart.
     *
     * @var string
     */
    protected $title = 'Динамика продаж';

    /**
     * Available options:
     * 'bar', 'line',
     * 'pie', 'percentage'.
     *
     * @var string
     */
    protected $type = 'line';
    protected $maxSlices = 500;

    protected $barOptions = [
        'spaceRatio' => 0.1,
        'stacked'    => 0,
        'height'     => 200,
        'depth'      => 1,
    ];

    protected $colors = [
        '#9a7fd1', '#5ab1ef', '#b6a2de', '#b6a2de',  '#ffb980', '#d87a80',
        '#8d98b3', '#e5cf0d', '#97b552', '#95706d', '#dc69aa',
        '#07a2a4', '#588dd5', '#f5994e', '#c05050',
        '#59678c', '#c9ab00', '#7eb00a', '#6f5553', '#c14089',
    ];
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the chart.
     *
     * @var string
     */
    protected $target = 'ProductOrderChart';
}

<?php

namespace Pragma\ProductTitle;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\Log;

class ProductTitle extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'product-title';

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute = null)
    {
        return $resource->toArray();
    }
}

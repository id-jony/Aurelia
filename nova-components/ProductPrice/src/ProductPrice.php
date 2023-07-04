<?php

namespace Pragma\ProductPrice;

use Laravel\Nova\Fields\Field;

class ProductPrice extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'product-price';

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

<?php

namespace App\Nova\Fields;

use Laravel\Nova\Fields\Field;

class ProductSelect extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'select-multiple-field';

    /**
     * Get the displayable name of the field.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?? __('Products');
    }

    /**
     * Set the options for the select field.
     *
     * @param  array  $options
     * @return $this
     */
    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

}

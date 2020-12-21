<?php

namespace Filament\Fields;

use Filament\Fields\Checkbox;

class Checkboxes extends BaseField {
    public $options = [];

    /**
     * @return static
     */
    public function options(array $options): self
    {
        foreach ($options as $key => $value) {
            $this->options[] = Checkbox::make($this->model.'.'.$key)->label($value);
        }

        return $this;
    }
}
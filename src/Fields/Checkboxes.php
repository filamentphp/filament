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
        foreach ($options as $value => $label) {
            $this->options[] = Checkbox::make($this->model)
                                    ->label($label)
                                    ->value($value);
        }

        return $this;
    }
}
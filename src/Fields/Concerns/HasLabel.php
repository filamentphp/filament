<?php

namespace Filament\Fields\Concerns;

trait HasLabel
{
    public $label;

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }
}

<?php

namespace Filament\Fields\Concerns;

trait HasPlaceholder
{
    public $placeholder;

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}

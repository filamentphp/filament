<?php

namespace Filament\FieldConcerns;

trait HasPlaceholder
{
    public $placeholder;

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}

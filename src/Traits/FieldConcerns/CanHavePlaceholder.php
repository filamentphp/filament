<?php

namespace Filament\Traits\FieldConcerns;

trait CanHavePlaceholder
{
    public $placeholder;

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}

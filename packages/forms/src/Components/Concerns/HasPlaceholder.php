<?php

namespace Filament\Forms\Components\Concerns;

trait HasPlaceholder
{
    protected $placeholder;

    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    public function placeholder($placeholder)
    {
        $this->configure(function () use ($placeholder) {
            $this->placeholder = $placeholder;
        });

        return $this;
    }
}

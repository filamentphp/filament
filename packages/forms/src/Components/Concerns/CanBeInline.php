<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeInline
{
    protected $isInline = false;

    public function inline()
    {
        $this->configure(function () {
            $this->isInline = true;
        });

        return $this;
    }

    public function stacked()
    {
        $this->configure(function () {
            $this->isInline = false;
        });

        return $this;
    }

    public function isInline()
    {
        return $this->isInline;
    }
}

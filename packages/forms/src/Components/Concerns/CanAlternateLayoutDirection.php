<?php

namespace Filament\Forms\Components\Concerns;

trait CanAlternateLayoutDirection
{
    protected $isInlineLayout = false;

    public function inline()
    {
        $this->configure(function () {
            $this->isInlineLayout = true;
        });

        return $this;
    }

    public function stacked()
    {
        $this->configure(function () {
            $this->isInlineLayout = false;
        });

        return $this;
    }

    public function isInlineLayout(): bool
    {
        return $this->isInlineLayout;
    }
}

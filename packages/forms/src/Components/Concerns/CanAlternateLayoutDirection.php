<?php

namespace Filament\Forms\Components\Concerns;

trait CanAlternateLayoutDirection
{
    protected $isInlineLayout = true;

    public function inlineLayout()
    {
        $this->configure(function () {
            $this->isInlineLayout = true;
        });

        return $this;
    }

    public function stackedLayout()
    {
        $this->configure(function () {
            $this->isInlineLayout = false;
        });

        return $this;
    }

    public function isInlineLayout()
    {
        return $this->isInlineLayout;
    }
}

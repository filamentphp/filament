<?php

namespace Filament\Forms\Components\Concerns;

trait CanAlternateLayoutDirection
{
    protected $inlineLayout = true;

    public function inlinelLayout()
    {
        $this->configure(function () {
            $this->inlineLayout = true;
        });

        return $this;
    }

    public function stackedLayout()
    {
        $this->configure(function () {
            $this->inlineLayout = false;
        });

        return $this;
    }

    public function isInlineLayout()
    {
        return $this->inlineLayout;
    }
}

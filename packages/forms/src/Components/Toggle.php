<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;

class Toggle extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanAlternateLayoutDirection;

    protected $onIcon;

    protected $offIcon;

    protected function setUp()
    {
        $this->default(false);
    }

    public function onIcon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->onIcon = $icon;
        });

        return $this;
    }

    public function offIcon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->offIcon = $icon;
        });

        return $this;
    }

    public function getOnIcon()
    {
        return $this->onIcon;
    }

    public function getOffIcon()
    {
        return $this->offIcon;
    }

    public function hasOffIcon()
    {
        return $this->offIcon !== null;
    }

    public function hasOnIcon()
    {
        return $this->onIcon !== null;
    }
}

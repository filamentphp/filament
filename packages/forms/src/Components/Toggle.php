<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Concerns;
use Filament\Forms\Components\Field;

class Toggle extends Field
{
    use Concerns\CanBeAutofocused;

    protected $onLabel;

    protected $offLabel;

    protected $onIcon;

    protected $offIcon;

    protected function setUp()
    {
        $this->default(false);
    }

    public function onLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->onLabel = $label;
        });

        return $this;
    }

    public function offLabel($label)
    {
        $this->configure(function () use ($label) {
            $this->offLabel = $label;
        });

        return $this;
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

    public function getOnLabel()
    {
        return $this->onLabel;
    }

    public function getOffLabel()
    {
        return $this->offLabel;
    }

    public function getOnIcon()
    {
        return $this->onIcon;
    }

    public function getOffIcon()
    {
        return $this->offIcon;
    }
}

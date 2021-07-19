<?php

namespace Filament\Forms\Components;

class Toggle extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeInline;

    protected $offIcon;

    protected $onIcon;

    protected function setUp()
    {
        $this->default(false);

        $this->inline();
    }

    public function getOffIcon()
    {
        return $this->offIcon;
    }

    public function getOnIcon()
    {
        return $this->onIcon;
    }

    public function hasOffIcon()
    {
        return $this->offIcon !== null;
    }

    public function hasOnIcon()
    {
        return $this->onIcon !== null;
    }

    public function offIcon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->offIcon = $icon;
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
}

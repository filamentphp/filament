<?php

namespace Filament\Tables\Columns;

class Boolean extends Icon
{
    protected $falseIcon = 'heroicon-o-x-circle';

    protected $trueIcon = 'heroicon-s-check-circle';

    protected function setUp()
    {
        $this->options([
            $this->getFalseIcon() => fn ($value) => ! $value,
            $this->getTrueIcon() => fn ($value) => $value,
        ]);
    }

    public function falseIcon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->falseIcon = $icon;

            $this->setUp();
        });

        return $this;
    }

    public function getFalseIcon()
    {
        return $this->falseIcon;
    }

    public function getTrueIcon()
    {
        return $this->trueIcon;
    }

    public function trueIcon($icon)
    {
        $this->configure(function () use ($icon) {
            $this->trueIcon = $icon;

            $this->setUp();
        });

        return $this;
    }
}

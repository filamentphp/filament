<?php

namespace Filament\Tables\Columns;

class Boolean extends Icon
{
    public $falseIcon = 'heroicon-o-x-circle';

    public $trueIcon = 'heroicon-s-check-circle';

    protected function setUp()
    {
        $this->options([
            $this->falseIcon => fn ($value) => ! $value,
            $this->trueIcon => fn ($value) => $value,
        ]);
    }

    public function falseIcon($icon)
    {
        $this->falseIcon = $icon;

        $this->setUp();

        return $this;
    }

    public function trueIcon($icon)
    {
        $this->trueIcon = $icon;

        $this->setUp();

        return $this;
    }
}

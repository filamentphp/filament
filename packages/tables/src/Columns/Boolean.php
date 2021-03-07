<?php

namespace Filament\Tables\Columns;

class Boolean extends Icon
{
    public $trueIcon = 'heroicon-s-check-circle';

    public $falseIcon = 'heroicon-o-x-circle';

    protected function setUp()
    {
        $this->options([
            $this->trueIcon => fn ($value) => $value,
            $this->falseIcon => fn ($value) => !$value,
        ]);
    }

    public function trueIcon($icon)
    {
        $this->trueIcon = $icon;

        $this->setUp();

        return $this;
    }

    public function falseIcon($icon)
    {
        $this->falseIcon = $icon;

        $this->setUp();

        return $this;
    }
}

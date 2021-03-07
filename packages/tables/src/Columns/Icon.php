<?php

namespace Filament\Tables\Columns;

class Icon extends Column
{
    public $options;

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }
}

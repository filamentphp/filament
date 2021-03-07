<?php

namespace Filament\Tables\Columns;

class Icon extends Column
{
    use Concerns\HasAction;

    public $options;

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }
}

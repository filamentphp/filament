<?php

namespace Filament\Tables\Columns;

class Text extends Column
{
    public $formatUsing;

    public function formatUsing($callback)
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function getValue($record, $attribute = null)
    {
        $value = parent::getValue($record);

        if ($this->formatUsing) {
            $callback = $this->formatUsing;

            $value = $callback($record);
        }

        return $value;
    }
}

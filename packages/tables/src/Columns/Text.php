<?php

namespace Filament\Tables\Columns;

class Text extends Column
{
    public $formatUsing;

    public $link;

    public function formatUsing($callback)
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function getLink($record)
    {
        if (is_callable($this->link)) {
            $callback = $this->link;
            
            return $callback($record);
        }

        return $this->link;
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

    public function link($link)
    {
        $this->link = $link;

        return $this;
    }
}

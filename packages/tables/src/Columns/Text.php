<?php

namespace Filament\Tables\Columns;

class Text extends Column
{
    public $formatUsing;

    public $url;

    public function formatUsing($callback)
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function getUrl($record)
    {
        if (is_callable($this->url)) {
            $callback = $this->url;

            return $callback($record);
        }

        return $this->url;
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

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}

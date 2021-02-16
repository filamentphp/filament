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

            $value = $callback($value);
        }

        return $value;
    }

    public function options($options)
    {
        $this->formatUsing = function ($value) use ($options) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }

            return $value;
        };

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}

<?php

namespace Filament\Tables\Columns;

use Carbon\Carbon;

class Text extends Column
{
    public $default;

    public $formatUsing;

    public $primary = false;

    public $url;

    public function currency($symbol = '$', $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        $this->formatUsing = function ($value) use ($decimalSeparator, $symbol, $thousandsSeparator) {
            if (! is_numeric($value)) return $this->default;

            return $symbol . number_format($value, 2, $decimalSeparator, $thousandsSeparator);
        };

        return $this;
    }

    public function date($format = 'F j, Y')
    {
        $this->formatUsing = function ($value) use ($format) {
            $value = Carbon::parse($value)->format($format);

            return $value;
        };

        return $this;
    }

    public function dateTime($format = 'F j, Y H:i:s')
    {
        $this->date($format);

        return $this;
    }

    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

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

        if ($value === null) {
            $this->default;
        }

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

    public function primary()
    {
        $this->primary = true;

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}

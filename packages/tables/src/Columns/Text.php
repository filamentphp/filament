<?php

namespace Filament\Tables\Columns;

use Carbon\Carbon;

class Text extends Column
{
    public $default;

    public $formatUsing;

    public $url;

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

    public function price($locale = null)
    {
        $this->formatUsing = function ($value) use ($locale) {
            if (! is_numeric($value)) return null;

            setlocale(LC_MONETARY, $locale ?: locale_get_default());

            $formatter = numfmt_create('en_US', \NumberFormatter::CURRENCY);
            $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);

            return numfmt_format_currency($formatter, $value, trim(localeconv()['int_curr_symbol']));
        };

        return $this;
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }
}

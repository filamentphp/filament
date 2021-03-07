<?php

namespace Filament\Tables\Columns;

use Carbon\Carbon;

class Text extends Column
{
    public $action;

    public $default;

    public $formatUsing;

    public $shouldOpenUrlInNewTab = false;

    public $url;

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    public function currency($symbol = '$', $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        $this->formatUsing = function ($value) use ($decimalSeparator, $symbol, $thousandsSeparator) {
            if (! is_numeric($value)) return $this->default;

            return $symbol.number_format($value, 2, $decimalSeparator, $thousandsSeparator);
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
        $value = parent::getValue($record, $attribute);

        if ($value === null) {
            return value($this->default);
        }

        if ($this->formatUsing) {
            $callback = $this->formatUsing;

            $value = $callback($value);
        }

        return $value;
    }

    public function openUrlInNewTab()
    {
        $this->shouldOpenUrlInNewTab = true;

        return $this;
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

    public function url($url, $shouldOpenInNewTab = false)
    {
        $this->url = $url;
        if ($shouldOpenInNewTab) $this->openUrlInNewTab();

        return $this;
    }
}

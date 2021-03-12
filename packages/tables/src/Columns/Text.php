<?php

namespace Filament\Tables\Columns;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Text extends Column
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    protected $defaultValue;

    protected $formatUsing;

    public function currency($symbol = '$', $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        $this->configure(function () use ($decimalSeparator, $symbol, $thousandsSeparator) {
            $this->formatUsing = function ($value) use ($decimalSeparator, $symbol, $thousandsSeparator) {
                if (! is_numeric($value)) return $this->getDefaultValue();

                return $symbol . number_format($value, 2, $decimalSeparator, $thousandsSeparator);
            };
        });

        return $this;
    }

    public function date($format = 'F j, Y')
    {
        $this->configure(function () use ($format) {
            $this->formatUsing = function ($value) use ($format) {
                $value = Carbon::parse($value)->format($format);

                return $value;
            };
        });

        return $this;
    }

    public function dateTime($format = 'F j, Y H:i:s')
    {
        $this->configure(function () use ($format) {
            $this->date($format);
        });

        return $this;
    }

    public function default($value)
    {
        $this->configure(function () use ($value) {
            $this->defaultValue = $value;
        });

        return $this;
    }

    public function formatUsing($callback)
    {
        $this->configure(function () use ($callback) {
            $this->formatUsing = $callback;
        });

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getValue($record, $attribute = null)
    {
        $value = parent::getValue($record, $attribute);

        if ($value === null) {
            return value($this->getDefaultValue());
        }

        if ($format = $this->formatUsing) {
            $value = $format($value);
        }

        return $value;
    }

    public function options($options)
    {
        $this->configure(function () use ($options) {
            $this->formatUsing = function ($value) use ($options) {
                if (array_key_exists($value, $options)) {
                    return $options[$value];
                }

                return $value;
            };
        });

        return $this;
    }

    public function limit($length = -1)
    {
        $this->configure(function () use ($length) {
            $this->formatUsing = function ($value) use ($length) {
                return Str::limit($value, $length);
            };
        });

        return $this;
    }
}

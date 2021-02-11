<?php

namespace Filament\FieldConcerns;

trait HasDateFormats
{
    public $displayFormat;

    public $format;

    public function displayFormat($format)
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }
}

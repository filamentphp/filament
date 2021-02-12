<?php

namespace Filament\Fields\Concerns;

trait CanBeLengthConstrained
{
    public $maxLength;

    public $minLength;

    public function maxLength($length)
    {
        $this->maxLength = $length;

        $this->addRules([$this->name => ["max:$length"]]);

        return $this;
    }

    public function minLength($length)
    {
        $this->minLength = $length;

        $this->addRules([$this->name => ["min:$length"]]);

        return $this;
    }
}

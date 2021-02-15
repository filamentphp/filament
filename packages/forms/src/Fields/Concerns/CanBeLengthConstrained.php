<?php

namespace Filament\Forms\Fields\Concerns;

trait CanBeLengthConstrained
{
    public $maxLength;

    public $minLength;

    public function maxLength($length)
    {
        $this->maxLength = $length;

        $this->addRules([$this->name => ["max:{$this->maxLength}"]]);

        return $this;
    }

    public function minLength($length)
    {
        $this->minLength = $length;

        $this->addRules([$this->name => ["min:{$this->minLength}"]]);

        return $this;
    }
}

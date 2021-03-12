<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeLengthConstrained
{
    protected $maxLength;

    protected $minLength;

    public function getMaxLength()
    {
        return $this->maxLength;
    }

    public function getMinLength()
    {
        return $this->minLength;
    }

    public function maxLength($length)
    {
        $this->maxLength = $length;

        $this->addRules([$this->getName() => ["max:{$this->maxLength}"]]);

        return $this;
    }

    public function minLength($length)
    {
        $this->minLength = $length;

        $this->addRules([$this->getName() => ["min:{$this->minLength}"]]);

        return $this;
    }
}

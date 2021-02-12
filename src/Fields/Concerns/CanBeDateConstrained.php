<?php

namespace Filament\Fields\Concerns;

trait CanBeDateConstrained
{
    public $maxDate;

    public $minDate;

    public function maxDate($date)
    {
        $this->maxDate = $date;

        $this->addRules([$this->name => ["before_or_equal:$date"]]);

        return $this;
    }

    public function minDate($date)
    {
        $this->minDate = $date;

        $this->addRules([$this->name => ["after_or_equal:$date"]]);

        return $this;
    }
}

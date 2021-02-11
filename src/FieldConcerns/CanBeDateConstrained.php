<?php

namespace Filament\FieldConcerns;

trait CanBeDateConstrained
{
    public $afterDate;

    public $beforeDate;

    public function afterDate($date)
    {
        $this->afterDate = $date;

        $this->addRules([$this->name => ["after:$date"]]);

        return $this;
    }

    public function beforeDate($date)
    {
        $this->beforeDate = $date;

        $this->addRules([$this->name => ["before:$date"]]);

        return $this;
    }
}

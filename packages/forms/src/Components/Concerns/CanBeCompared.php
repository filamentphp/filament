<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeCompared
{
    public function confirmed($confirmationFieldName = null)
    {
        $this->configure(function () use ($confirmationFieldName) {
            if ($confirmationFieldName === null) {
                $confirmationFieldName = "{$this->getName()}Confirmation";
            }

            $this->addRules([$confirmationFieldName => ["same:{$this->getName()}"]]);
        });

        return $this;
    }

    public function same($field)
    {
        $this->configure(function () use ($field) {
            $this->addRules([$this->getName() => ["same:{$field}"]]);
        });

        return $this;
    }
}

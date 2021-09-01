<?php

namespace Filament\Forms\Components\Concerns;

trait EvaluatesCallbacks
{
    protected function evaluate(&$value, array $parameters = [])
    {
        if (is_callable($value)) {
            return $value = app()->call($value, array_merge([
                'component' => $this,
                'get' => $this->getGetCallback(),
                'livewire' => $this->getLivewire(),
                'model' => $this->getModel(),
                'set' => $this->getSetCallback(),
                'state' => $this->getState(),
            ], $parameters));
        }

        return $value;
    }
}

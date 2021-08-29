<?php

namespace Filament\Forms\Components\Concerns;

trait EvaluatesCallbacks
{
    protected function evaluate(&$value, array $parameters = [])
    {
        if (is_callable($value)) {
            return $value = app()->call($value, array_merge([
                'component' => $this,
                'livewire' => $this->getLivewire(),
                'model' => $this->getModel(),
                'state' => $this->getState(),
            ], $parameters));
        }

        return $value;
    }
}

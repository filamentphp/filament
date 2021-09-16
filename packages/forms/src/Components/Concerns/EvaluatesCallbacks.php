<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait EvaluatesCallbacks
{
    protected function evaluate(&$value, array $parameters = [])
    {
        if ($value instanceof Closure) {
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

<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait EvaluatesCallbacks
{
    protected function evaluate($value, array $parameters = [])
    {
        if ($value instanceof Closure) {
            $model = $this->getModel();

            return app()->call($value, array_merge([
                'component' => $this,
                'get' => $this->getGetCallback(),
                'livewire' => $this->getLivewire(),
                'model' => $model,
                'record' => $model instanceof Model ? $model : null,
                'set' => $this->getSetCallback(),
                'state' => $this->getState(),
            ], $parameters));
        }

        return $value;
    }
}

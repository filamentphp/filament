<?php

namespace Filament\Forms\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait EvaluatesClosures
{
    protected function evaluate($value, array $parameters = [])
    {
        if ($value instanceof Closure) {
            $model = $this->getModel();

            return app()->call($value, array_merge([
                'container' => $this,
                'livewire' => $this->getLivewire(),
                'model' => $model,
                'record' => $model instanceof Model ? $model : null,
            ], $parameters));
        }

        return $value;
    }
}

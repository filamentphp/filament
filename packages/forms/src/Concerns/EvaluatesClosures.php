<?php

namespace Filament\Forms\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait EvaluatesClosures
{
    public function evaluate($value, array $parameters = [])
    {
        if ($value instanceof Closure) {
            return app()->call(
                $value,
                array_merge($this->getDefaultEvaluationParameters(), $parameters),
            );
        }

        return $value;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        $model = $this->getModel();

        return [
            'container' => $this,
            'livewire' => $this->getLivewire(),
            'model' => $model,
            'record' => $model instanceof Model ? $model : null,
        ];
    }
}

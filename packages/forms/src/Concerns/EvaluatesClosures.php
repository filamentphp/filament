<?php

namespace Filament\Forms\Concerns;

use Closure;

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
        return [
            'container' => $this,
            'livewire' => $this->getLivewire(),
            'model' => $this->getModel(),
            'record' => $this->getRecord(),
        ];
    }
}

<?php

namespace Filament\Tables\Columns\Concerns;

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
            'column' => $this,
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ];
    }
}

<?php

namespace Filament\Forms\Components\Concerns;

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
            'component' => $this,
            'get' => $this->getGetCallback(),
            'livewire' => $this->getLivewire(),
            'model' => $this->getModel(),
            'record' => $this->getRecord(),
            'set' => $this->getSetCallback(),
            'state' => $this->shouldEvaluateWithState() ? $this->getState() : null,
        ];
    }

    protected function shouldEvaluateWithState(): bool
    {
        return true;
    }
}

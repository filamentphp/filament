<?php

namespace Filament\Forms\Components\Concerns;

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
            'component' => $this,
            'get' => $this->getGetCallback(),
            'livewire' => $this->getLivewire(),
            'model' => $model,
            'record' => $model instanceof Model ? $model : null,
            'set' => $this->getSetCallback(),
            'state' => $this->shouldEvaluateWithState() ? $this->getState() : null,
        ];
    }

    protected function shouldEvaluateWithState(): bool
    {
        return true;
    }
}

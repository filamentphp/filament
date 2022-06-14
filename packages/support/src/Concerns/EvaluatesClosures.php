<?php

namespace Filament\Support\Concerns;

use Closure;

trait EvaluatesClosures
{
    protected string $evaluationIdentifier;

    public function evaluate($value, array $parameters = [])
    {
        if ($value instanceof Closure) {
            return app()->call(
                $value,
                array_merge(
                    isset($this->evaluationIdentifier) ? [$this->evaluationIdentifier => $this] : [],
                    $this->getDefaultEvaluationParameters(),
                    $parameters,
                ),
            );
        }

        return $value;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return [];
    }
}

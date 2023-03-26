<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Evaluator;

trait EvaluatesClosures
{
    protected string $evaluationIdentifier;

    /**
     * @template T
     *
     * @param  T | callable(): T  $value
     * @param  array<string, mixed>  $parameters
     * @return T
     */
    public function evaluate(mixed $value, array $parameters = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        return app(Evaluator::class)(
            $value,
            array_merge(
                isset($this->evaluationIdentifier) ? [$this->evaluationIdentifier => $this] : [],
                $this->getDefaultEvaluationParameters(),
                $parameters,
            ),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return [];
    }
}

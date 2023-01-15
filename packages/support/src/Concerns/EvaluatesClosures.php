<?php

namespace Filament\Support\Concerns;

use Closure;

trait EvaluatesClosures
{
    protected string $evaluationIdentifier;

    /**
     * @var array<string>
     */
    protected array $evaluationParametersToRemove = [];

    /**
     * @template T
     *
     * @param  T | callable(): T  $value
     * @param  array<string, mixed>  $parameters
     * @param  array<string>  $exceptParameters
     * @return T
     */
    public function evaluate(mixed $value, array $parameters = [], array $exceptParameters = []): mixed
    {
        if (! $value instanceof Closure) {
            return $value;
        }

        $this->evaluationParametersToRemove = $exceptParameters;

        return app()->call(
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

    protected function resolveEvaluationParameter(string $parameter, Closure $value): mixed
    {
        if ($this->isEvaluationParameterRemoved($parameter)) {
            return null;
        }

        return $value();
    }

    protected function isEvaluationParameterRemoved(string $parameter): bool
    {
        return in_array($parameter, $this->evaluationParametersToRemove);
    }
}

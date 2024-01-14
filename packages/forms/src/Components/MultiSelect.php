<?php

namespace Filament\Forms\Components;

/**
 * @deprecated Use `Select` with the `multiple()` method instead.
 */
class MultiSelect extends Select
{
    protected string $evaluationIdentifier = 'multiSelect';

    public function isMultiple(): bool
    {
        return true;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'select' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

<?php

namespace Filament\Forms\Components;

/**
 * @deprecated Use `Select` with the `multiple()` method instead.
 */
class MultiSelect extends Select
{
    public function isMultiple(): bool
    {
        return true;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'multiSelect' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

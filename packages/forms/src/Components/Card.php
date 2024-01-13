<?php

namespace Filament\Forms\Components;

/**
 * @deprecated Use `Section` with an empty heading instead.
 */
class Card extends Section
{
    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'card' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

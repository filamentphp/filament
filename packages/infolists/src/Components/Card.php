<?php

namespace Filament\Infolists\Components;

/**
 * @deprecated Use `Section` with an empty heading instead.
 */
class Card extends Section
{
    protected string $evaluationIdentifier = 'card';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'section' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

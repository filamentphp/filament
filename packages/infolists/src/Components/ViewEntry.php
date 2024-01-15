<?php

namespace Filament\Infolists\Components;

class ViewEntry extends Entry
{
    protected string $evaluationIdentifier = 'viewEntry';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'entry', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

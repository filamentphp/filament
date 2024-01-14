<?php

namespace Filament\Forms\Components;

class ViewField extends Field
{
    protected string $evaluationIdentifier = 'viewField';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'field', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

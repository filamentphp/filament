<?php

namespace Filament\Infolists\Components;

class ViewEntry extends Entry
{

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'viewEntry', 'entry', 'component' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

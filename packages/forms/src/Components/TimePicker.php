<?php

namespace Filament\Forms\Components;

class TimePicker extends DateTimePicker
{
    public function hasDate(): bool
    {
        return false;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'timePicker' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

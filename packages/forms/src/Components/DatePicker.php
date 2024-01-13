<?php

namespace Filament\Forms\Components;

class DatePicker extends DateTimePicker
{
    public function hasTime(): bool
    {
        return false;
    }
    
    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match($parameterName) {
            'datePicker' => [$this],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}

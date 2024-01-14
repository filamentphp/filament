<?php

namespace Filament\Forms\Components;

class DatePicker extends DateTimePicker
{
    protected string $evaluationIdentifier = 'datePicker';

    public function hasTime(): bool
    {
        return false;
    }
}

<?php

namespace Filament\Forms\Components;

class TimePicker extends DateTimePicker
{
    protected string $evaluationIdentifier = 'timePicker';

    public function hasDate(): bool
    {
        return false;
    }
}

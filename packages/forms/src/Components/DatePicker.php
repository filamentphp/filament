<?php

namespace Filament\Forms\Components;

use Closure;

class DatePicker extends DateTimePicker
{
    public function hasTime(): bool
    {
        return false;
    }
    
    public function timezone( Closure|string|null $timezone ): static {
        return $this;
    }
}

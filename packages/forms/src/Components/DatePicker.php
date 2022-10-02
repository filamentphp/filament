<?php

namespace Filament\Forms\Components;

class DatePicker extends DateTimePicker
{
    protected array $disabledDates = [];

    public function hasTime(): bool
    {
        return false;
    }

    public function disabledDates(array $dates): static
    {
        $this->disabledDates = $dates;

        return $this;
    }

    public function getDisabledDates(): ?array
    {
        return $this->evaluate($this->disabledDates);
    }
}

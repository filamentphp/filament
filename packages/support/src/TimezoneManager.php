<?php

namespace Filament\Support;

class TimezoneManager
{
    protected ?string $timezone = null;

    public function set(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function get(): string
    {
        return $this->timezone ?? config('app.timezone');
    }
}

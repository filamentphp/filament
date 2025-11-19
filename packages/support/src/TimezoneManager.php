<?php

namespace Filament\Support;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

class TimezoneManager
{
    use EvaluatesClosures;

    protected string | Closure | null $timezone = null;

    public function set(string | Closure | null $timezone = null): void
    {
        $this->timezone = $timezone;
    }

    public function get(): string
    {
        if (is_null($this->timezone)) {
            return config('app.timezone');
        }

        return (string) $this->evaluate($this->timezone);
    }
}

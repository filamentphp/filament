<?php

namespace Filament\Schemas\Components\StateCasts;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Support\Carbon;

class DateTimeStateCast implements StateCast
{
    public function __construct(
        protected string $format,
        protected string $internalFormat,
        protected string $timezone,
    ) {}

    public function get(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        if (! $state instanceof CarbonInterface) {
            $state = Carbon::parse($state);
        }

        $state->shiftTimezone($this->timezone);
        $state->setTimezone(config('app.timezone'));

        return $state->format($this->format);
    }

    public function set(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        if (! $state instanceof CarbonInterface) {
            try {
                $state = Carbon::createFromFormat($this->format, (string) $state, config('app.timezone'));
            } catch (InvalidFormatException) {
                try {
                    $state = Carbon::parse($state, config('app.timezone'));
                } catch (InvalidFormatException) {
                    return null;
                }
            }
        }

        $state = $state->setTimezone($this->timezone);

        return $state->format($this->internalFormat);
    }
}

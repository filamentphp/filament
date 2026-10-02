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
        protected ?string $timezone,
    ) {}

    public function get(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        if (! $state instanceof CarbonInterface) {
            $parsedState = Carbon::parse($state, $this->timezone === null ? 'UTC' : null);

            if (($this->timezone !== null) && is_string($state) && preg_match('/^(?:\d{4}-\d{2}-\d{2}[ T])?\d{2}:\d{2}(?::\d{2}(?:\.\d{1,6})?)?$/D', $state)) {
                $parts = date_parse($state);

                if (! $parts['warning_count']) {
                    foreach (['year', 'month', 'day', 'hour', 'minute', 'second'] as $part) {
                        if (($parts[$part] === false) || ($parts[$part] === $parsedState->{$part})) {
                            continue;
                        }

                        // Recover wall time normalized by an app-zone gap without changing the usual fold choice.
                        if ($parts['year'] === false) {
                            $state = Carbon::now()->toDateString() . ' ' . $state;
                        }

                        $parsedState = Carbon::parse($state, $this->timezone);

                        break;
                    }
                }
            }

            $state = $parsedState;
        }

        if ($this->timezone !== null) {
            $state = $state->shiftTimezone($this->timezone);
            $state = $state->setTimezone(config('app.timezone'));
        }

        return $state->format($this->format);
    }

    public function set(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        if (! $state instanceof CarbonInterface) {
            try {
                // Default omitted date parts to the app's calendar date, but omitted time parts to midnight.
                $state = Carbon::createFromFormat(
                    $this->timezone === null ? "Y-m-d {$this->format}|" : $this->format,
                    ($this->timezone === null ? Carbon::now(config('app.timezone'))->toDateString() . ' ' : '') . $state,
                    $this->timezone === null ? 'UTC' : config('app.timezone'),
                );
            } catch (InvalidFormatException) {
                try {
                    $state = Carbon::parse(
                        $state,
                        ($this->timezone === null) && (! Carbon::hasRelativeKeywords((string) $state))
                            ? 'UTC'
                            : config('app.timezone'),
                    );
                } catch (InvalidFormatException) {
                    return null;
                }
            }
        }

        if ($this->timezone !== null) {
            $state = $state->setTimezone($this->timezone);
        }

        return $state->format($this->internalFormat);
    }
}

<?php

namespace Filament\Tables\Columns\Concerns;

use Akaunting\Money;
use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $format ??= config('tables.date_format');

        $timezone ??= config('app.timezone');

        $this->formatStateUsing(static fn ($state): ?string => $state ? Carbon::parse($state)->setTimezone($timezone)->translatedFormat($format) : null);

        return $this;
    }

    public function dateTime(?string $format = null, ?string $timezone = null): static
    {
        $format ??= config('tables.date_time_format');

        $this->date($format, $timezone);

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->formatStateUsing(static fn ($state): ?string => $state ? Carbon::parse($state)->setTimezone($timezone)->diffForHumans() : null);

        return $this;
    }
    
    public function enum(array | Arrayable $options, $default = null): static
    {
        $this->formatStateUsing(static fn ($state): ?string => $options[$state] ?? ($default ?? $state));

        return $this;
    }

    public function limit(int $length = 100, string $end = '...'): static
    {
        $this->formatStateUsing(static function ($state) use ($length, $end): ?string {
            if (blank($state)) {
                return null;
            }

            return Str::limit($state, $length, $end);
        });

        return $this;
    }

    public function prefix(string | Closure $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Closure $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function html(): static
    {
        return $this->formatStateUsing(static fn ($state): HtmlString => new HtmlString($state));
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function money(string | Closure $currency = 'usd', bool $shouldConvert = false): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($currency, $shouldConvert): ?string {
            if (blank($state)) {
                return null;
            }

            return (new Money\Money(
                $state,
                (new Money\Currency(strtoupper($column->evaluate($currency)))),
                $shouldConvert,
            ))->format();
        });

        return $this;
    }

    public function getFormattedState()
    {
        $state = $this->getState();

        if ($this->formatStateUsing) {
            $state = $this->evaluate($this->formatStateUsing, [
                'state' => $state,
            ]);
        }

        if ($this->prefix) {
            $state = $this->evaluate($this->prefix) . $state;
        }

        if ($this->suffix) {
            $state = $state . $this->evaluate($this->suffix);
        }

        return $state;
    }

    public function time(?string $format = null, ?string $timezone = null): static
    {
        $format ??= config('tables.time_format');

        $this->date($format, $timezone);

        return $this;
    }
}

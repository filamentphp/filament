<?php

namespace Filament\Tables\Columns\Concerns;

use Akaunting\Money;
use Closure;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected ?int $limit = null;

    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    protected string | Closure | null $timezone = null;

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultDateDisplayFormat;

        $this->formatStateUsing(static function (Column $column, $state) use ($format, $timezone): ?string {
            /** @var TextColumn $column */
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $column->getTimezone())
                ->translatedFormat($format);
        });

        return $this;
    }

    public function dateTime(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultDateTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($timezone): ?string {
            /** @var TextColumn $column */
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $column->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function limit(int $length = 100, string $end = '...'): static
    {
        $this->limit = $length;

        $this->formatStateUsing(static function ($state) use ($end, $length): ?string {
            if (blank($state)) {
                return null;
            }

            return Str::limit($state, $length, $end);
        });

        return $this;
    }

    public function words(int $words = 100, string $end = '...'): static
    {
        $this->formatStateUsing(static function ($state) use ($words, $end): ?string {
            if (blank($state)) {
                return null;
            }

            return Str::words($state, $words, $end);
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
        return $this->formatStateUsing(static fn ($state): HtmlString => $state instanceof HtmlString ? $state : str($state)->sanitizeHtml()->toHtmlString());
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function money(string | Closure | null $currency = null, bool $shouldConvert = true): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($currency, $shouldConvert): ?string {
            if (blank($state)) {
                return null;
            }

            if (blank($currency)) {
                $currency = config('money.default_currency') ?? 'usd';
            }

            return (new Money\Money(
                $state,
                (new Money\Currency(strtoupper($column->evaluate($currency)))),
                $shouldConvert,
            ))->format();
        });

        return $this;
    }

    public function numeric(int | Closure $decimalPlaces = 0, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
    {
        $this->formatStateUsing(static function (Column $column, $state) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            return number_format(
                $state,
                $column->evaluate($decimalPlaces),
                $column->evaluate($decimalSeparator),
                $column->evaluate($thousandsSeparator),
            );
        });

        return $this;
    }

    public function getFormattedState()
    {
        $state = $this->evaluate(
            $this->formatArrayStateUsing ??
            $this->formatStateUsing ??
                fn ($state) => $state);

        if ($this->prefix) {
            $state = $this->evaluate($this->prefix) . $state;
        }

        if ($this->suffix) {
            $state = $state . $this->evaluate($this->suffix);
        }

        return $state;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function time(?string $format = null, ?string $timezone = null): static
    {
        $format ??= Table::$defaultTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }
}

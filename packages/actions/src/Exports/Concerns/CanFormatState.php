<?php

namespace Filament\Actions\Exports\Concerns;

use Closure;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use function Filament\Support\format_money;
use function Filament\Support\format_number;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected int | Closure | null $characterLimit = null;

    protected string | Closure | null $characterLimitEnd = null;

    protected int | Closure | null $wordLimit = null;

    protected string | Closure | null $wordLimitEnd = null;

    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    protected string | Closure | null $timezone = null;

    protected bool $isDate = false;

    protected bool $isDateTime = false;

    protected bool $isMoney = false;

    protected bool $isNumeric = false;

    protected bool $isTime = false;

    protected bool $isListedAsJson = false;

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $this->isDate = true;

        $format ??= Exporter::$defaultDateDisplayFormat;

        $this->formatStateUsing(static function (ExportColumn $column, $state) use ($format, $timezone): ?string {
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
        $this->isDateTime = true;

        $format ??= Exporter::$defaultDateTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->isDateTime = true;

        $this->formatStateUsing(static function (ExportColumn $column, $state) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $column->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function money(string | Closure | null $currency = null, int $divideBy = 0): static
    {
        $this->isMoney = true;

        $this->formatStateUsing(static function (ExportColumn $column, $state) use ($currency, $divideBy): ?string {
            if (blank($state)) {
                return null;
            }

            $currency = $column->evaluate($currency) ?? Exporter::$defaultCurrency;

            return format_money($state, $currency, $divideBy);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(static function (ExportColumn $column, $state) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            if ($decimalPlaces === null) {
                return format_number($state);
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

    public function time(?string $format = null, ?string $timezone = null): static
    {
        $this->isTime = true;

        $format ??= Exporter::$defaultTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function timezone(string | Closure | null $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function limit(int | Closure | null $length = 100, string | Closure | null $end = '...'): static
    {
        $this->characterLimit = $length;
        $this->characterLimitEnd = $end;

        return $this;
    }

    public function words(int $words = 100, string $end = '...'): static
    {
        $this->wordLimit = $words;
        $this->wordLimitEnd = $end;

        return $this;
    }

    public function prefix(string | Closure | null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Closure | null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function formatState(mixed $state): mixed
    {
        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

        if ($state instanceof LabelInterface) {
            $state = $state->getLabel();
        }

        if ($characterLimit = $this->getCharacterLimit()) {
            $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd());
        }

        if ($wordLimit = $this->getWordLimit()) {
            $state = Str::words($state, $wordLimit, $this->getWordLimitEnd());
        }

        $prefix = $this->getPrefix();
        $suffix = $this->getSuffix();

        if (filled($prefix)) {
            $state = $prefix . $state;
        }

        if (filled($suffix)) {
            $state = $state . $suffix;
        }

        return $state;
    }

    public function getFormattedState(): string
    {
        $state = $this->getState();

        if (! is_array($state)) {
            return $this->formatState($state);
        }

        $state = array_map($this->formatState(...), $state);

        if ($this->isListedAsJson()) {
            return json_encode($state);
        }

        return implode(', ', $state);
    }

    public function getCharacterLimit(): ?int
    {
        return $this->evaluate($this->characterLimit);
    }

    public function getCharacterLimitEnd(): ?string
    {
        return $this->evaluate($this->characterLimitEnd);
    }

    public function getWordLimit(): ?int
    {
        return $this->evaluate($this->wordLimit);
    }

    public function getWordLimitEnd(): ?string
    {
        return $this->evaluate($this->wordLimitEnd);
    }

    public function getTimezone(): string
    {
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function getPrefix(): ?string
    {
        return $this->evaluate($this->prefix);
    }

    public function getSuffix(): ?string
    {
        return $this->evaluate($this->suffix);
    }

    public function isDate(): bool
    {
        return $this->isDate;
    }

    public function isDateTime(): bool
    {
        return $this->isDateTime;
    }

    public function isMoney(): bool
    {
        return $this->isMoney;
    }

    public function isNumeric(): bool
    {
        return $this->isNumeric;
    }

    public function isTime(): bool
    {
        return $this->isTime;
    }

    public function listAsJson(bool $condition = true): static
    {
        $this->isListedAsJson = $condition;

        return $this;
    }

    public function isListedAsJson(): bool
    {
        return $this->isListedAsJson;
    }
}

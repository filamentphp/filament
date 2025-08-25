<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\Concerns\CanConfigureCommonMark;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Support\Enums\ArgumentValue;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

trait CanFormatState
{
    use CanConfigureCommonMark;

    protected ?Closure $formatStateUsing = null;

    protected int | Closure | null $characterLimit = null;

    protected string | Closure | null $characterLimitEnd = null;

    protected int | Closure | null $wordLimit = null;

    protected string | Closure | null $wordLimitEnd = null;

    protected string | Htmlable | Closure | null $prefix = null;

    protected string | Htmlable | Closure | null $suffix = null;

    protected string | Closure | null $timezone = null;

    protected bool | Closure $isHtml = false;

    protected bool | Closure $isMarkdown = false;

    protected bool $isDate = false;

    protected bool $isDateTime = false;

    protected bool $isMoney = false;

    protected bool $isNumeric = false;

    protected bool $isTime = false;

    public function markdown(bool | Closure $condition = true): static
    {
        $this->isMarkdown = $condition;

        return $this;
    }

    public function date(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDate = true;

        $this->formatStateUsing(static function (TextColumn $column, $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->translatedFormat($column->evaluate($format) ?? $column->getTable()->getDefaultDateDisplayFormat());
        });

        return $this;
    }

    public function dateTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultDateTimeDisplayFormat();

        $this->date($format, $timezone);

        return $this;
    }

    public function isoDate(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDate = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateDisplayFormat();

        $this->formatStateUsing(static function (TextColumn $column, $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->isoFormat($column->evaluate($format) ?? $column->getTable()->getDefaultIsoDateDisplayFormat());
        });

        return $this;
    }

    public function isoDateTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateTimeDisplayFormat();

        $this->isoDate($format, $timezone);

        return $this;
    }

    public function since(string | Closure | null $timezone = null): static
    {
        $this->isDateTime = true;

        $this->formatStateUsing(static function (TextColumn $column, $state) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function dateTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->tooltip(static function (TextColumn $column, mixed $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->translatedFormat($column->evaluate($format) ?? $column->getTable()->getDefaultDateDisplayFormat());
        });

        return $this;
    }

    public function dateTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultDateTimeDisplayFormat();

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function timeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultTimeDisplayFormat();

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function sinceTooltip(string | Closure | null $timezone = null): static
    {
        $this->tooltip(static function (TextColumn $column, mixed $state) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function isoDateTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateDisplayFormat();

        $this->tooltip(static function (TextColumn $column, mixed $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($column->evaluate($timezone) ?? $column->getTimezone())
                ->isoFormat($column->evaluate($format) ?? $column->getTable()->getDefaultIsoDateDisplayFormat());
        });

        return $this;
    }

    public function isoDateTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoDateTimeDisplayFormat();

        $this->isoDateTooltip($format, $timezone);

        return $this;
    }

    public function isoTimeTooltip(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoTimeDisplayFormat();

        $this->isoDateTooltip($format, $timezone);

        return $this;
    }

    public function money(string | Closure | null $currency = null, int | Closure $divideBy = 0, string | Closure | null $locale = null, int | Closure | null $decimalPlaces = null): static
    {
        $this->isMoney = true;

        $this->formatStateUsing(static function (TextColumn $column, $state) use ($currency, $divideBy, $locale, $decimalPlaces): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $currency = $column->evaluate($currency) ?? $column->getTable()->getDefaultCurrency();
            $locale = $column->evaluate($locale) ?? $column->getTable()->getDefaultNumberLocale() ?? config('app.locale');
            $decimalPlaces = $column->evaluate($decimalPlaces);

            if ($divideBy = $column->evaluate($divideBy)) {
                $state /= $divideBy;
            }

            return Number::currency($state, $currency, $locale, $decimalPlaces);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null | ArgumentValue $decimalSeparator = ArgumentValue::Default, string | Closure | null | ArgumentValue $thousandsSeparator = ArgumentValue::Default, int | Closure | null $maxDecimalPlaces = null, string | Closure | null $locale = null): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(static function (TextColumn $column, $state) use ($decimalPlaces, $decimalSeparator, $locale, $maxDecimalPlaces, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $decimalPlaces = $column->evaluate($decimalPlaces);
            $decimalSeparator = $column->evaluate($decimalSeparator);
            $thousandsSeparator = $column->evaluate($thousandsSeparator);

            if (
                ($decimalSeparator !== ArgumentValue::Default) ||
                ($thousandsSeparator !== ArgumentValue::Default)
            ) {
                return number_format(
                    $state,
                    $decimalPlaces,
                    $decimalSeparator === ArgumentValue::Default ? '.' : $decimalSeparator,
                    $thousandsSeparator === ArgumentValue::Default ? ',' : $thousandsSeparator,
                );
            }

            $locale = $column->evaluate($locale) ?? $column->getTable()->getDefaultNumberLocale() ?? config('app.locale');

            return Number::format($state, $decimalPlaces, $column->evaluate($maxDecimalPlaces), $locale);
        });

        return $this;
    }

    public function time(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultTimeDisplayFormat();

        $this->date($format, $timezone);

        return $this;
    }

    public function isoTime(string | Closure | null $format = null, string | Closure | null $timezone = null): static
    {
        $this->isTime = true;

        $format ??= fn (TextColumn $column): string => $column->getTable()->getDefaultIsoTimeDisplayFormat();

        $this->isoDate($format, $timezone);

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

    public function words(int | Closure | null $words = 100, string | Closure | null $end = '...'): static
    {
        $this->wordLimit = $words;
        $this->wordLimitEnd = $end;

        return $this;
    }

    public function prefix(string | Htmlable | Closure | null $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Htmlable | Closure | null $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function html(bool | Closure $condition = true): static
    {
        $this->isHtml = $condition;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function formatState(mixed $state): mixed
    {
        $isHtml = $this->isHtml();

        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

        if (is_array($state)) {
            $state = json_encode($state);
        }

        if ($isHtml) {
            if ($this->isMarkdown()) {
                $state = Str::markdown($state, $this->getCommonMarkOptions(), $this->getCommonMarkExtensions());
            }

            $state = Str::sanitizeHtml($state);
        }

        if ($state instanceof Htmlable) {
            $isHtml = true;
            $state = $state->toHtml();
        }

        if ($state instanceof LabelInterface) {
            $state = $state->getLabel();
        }

        if (! $isHtml) {
            if ($characterLimit = $this->getCharacterLimit()) {
                $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd());
            }

            if ($wordLimit = $this->getWordLimit()) {
                $state = Str::words($state, $wordLimit, $this->getWordLimitEnd());
            }
        }

        $prefix = $this->getPrefix();
        $suffix = $this->getSuffix();

        if (
            (($prefix instanceof Htmlable) || ($suffix instanceof Htmlable)) &&
            (! $isHtml)
        ) {
            $isHtml = true;
            $state = e($state);
        }

        if (filled($prefix)) {
            if ($prefix instanceof Htmlable) {
                $prefix = $prefix->toHtml();
            } elseif ($isHtml) {
                $prefix = e($prefix);
            }

            $state = $prefix . $state;
        }

        if (filled($suffix)) {
            if ($suffix instanceof Htmlable) {
                $suffix = $suffix->toHtml();
            } elseif ($isHtml) {
                $suffix = e($suffix);
            }

            $state .= $suffix;
        }

        return $isHtml ? new HtmlString($state) : $state;
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
        return $this->evaluate($this->timezone) ?? ($this->isDateTime() ? FilamentTimezone::get() : config('app.timezone'));
    }

    public function isHtml(): bool
    {
        return $this->evaluate($this->isHtml) || $this->isMarkdown();
    }

    public function getPrefix(): string | Htmlable | null
    {
        return $this->evaluate($this->prefix);
    }

    public function getSuffix(): string | Htmlable | null
    {
        return $this->evaluate($this->suffix);
    }

    public function isMarkdown(): bool
    {
        return (bool) $this->evaluate($this->isMarkdown);
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
}

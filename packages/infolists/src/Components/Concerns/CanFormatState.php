<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Filament\Support\Enums\ArgumentValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

trait CanFormatState
{
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

    public function date(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $this->isDate = true;

        $format ??= Infolist::$defaultDateDisplayFormat;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $component->getTimezone())
                ->translatedFormat($component->evaluate($format));
        });

        return $this;
    }

    public function dateTime(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $this->isDateTime = true;

        $format ??= Infolist::$defaultDateTimeDisplayFormat;

        $this->date($format, $timezone);

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->isDateTime = true;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $component->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function dateTooltip(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $format ??= Infolist::$defaultDateDisplayFormat;

        $this->tooltip(static function (TextEntry $component, mixed $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $component->getTimezone())
                ->translatedFormat($component->evaluate($format));
        });

        return $this;
    }

    public function dateTimeTooltip(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $format ??= Infolist::$defaultDateTimeDisplayFormat;

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function timeTooltip(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $format ??= Infolist::$defaultTimeDisplayFormat;

        $this->dateTooltip($format, $timezone);

        return $this;
    }

    public function sinceTooltip(?string $timezone = null): static
    {
        $this->tooltip(static function (TextEntry $component, mixed $state) use ($timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $component->getTimezone())
                ->diffForHumans();
        });

        return $this;
    }

    public function money(string | Closure | null $currency = null, int $divideBy = 0, string | Closure | null $locale = null): static
    {
        $this->isMoney = true;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($currency, $divideBy, $locale): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $currency = $component->evaluate($currency) ?? Infolist::$defaultCurrency;
            $locale = $component->evaluate($locale) ?? Infolist::$defaultNumberLocale ?? config('app.locale');

            if ($divideBy) {
                $state /= $divideBy;
            }

            return Number::currency($state, $currency, $locale);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null | ArgumentValue $decimalSeparator = ArgumentValue::Default, string | Closure | null | ArgumentValue $thousandsSeparator = ArgumentValue::Default, int | Closure | null $maxDecimalPlaces = null, string | Closure | null $locale = null): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($decimalPlaces, $decimalSeparator, $locale, $maxDecimalPlaces, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $decimalPlaces = $component->evaluate($decimalPlaces);
            $decimalSeparator = $component->evaluate($decimalSeparator);
            $thousandsSeparator = $component->evaluate($thousandsSeparator);

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

            $locale = $component->evaluate($locale) ?? Infolist::$defaultNumberLocale ?? config('app.locale');

            return Number::format($state, $decimalPlaces, $component->evaluate($maxDecimalPlaces), $locale);
        });

        return $this;
    }

    public function time(string | Closure | null $format = null, ?string $timezone = null): static
    {
        $this->isTime = true;

        $format ??= Infolist::$defaultTimeDisplayFormat;

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

        if ($isHtml) {
            $state = Str::sanitizeHtml($state);
        }

        if ($state instanceof Htmlable) {
            $isHtml = true;
            $state = $state->toHtml();
        }

        if ($state instanceof LabelInterface) {
            $state = $state->getLabel();
        }

        if ($characterLimit = $this->getCharacterLimit()) {
            $state = Str::limit($state, $characterLimit, $this->getCharacterLimitEnd());
        }

        if ($wordLimit = $this->getWordLimit()) {
            $state = Str::words($state, $wordLimit, $this->getWordLimitEnd());
        }

        if ($isHtml && $this->isMarkdown()) {
            $state = Str::markdown($state);
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

            $state = $state . $suffix;
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
        return $this->evaluate($this->timezone) ?? config('app.timezone');
    }

    public function isHtml(): bool
    {
        return $this->evaluate($this->isHtml) || $this->isMarkdown() || $this->isProse();
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

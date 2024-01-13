<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Htmlable;
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

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $this->isDate = true;

        $format ??= Infolist::$defaultDateDisplayFormat;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($format, $timezone): ?string {
            if (blank($state)) {
                return null;
            }

            return Carbon::parse($state)
                ->setTimezone($timezone ?? $component->getTimezone())
                ->translatedFormat($format);
        });

        return $this;
    }

    public function dateTime(?string $format = null, ?string $timezone = null): static
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

    public function money(string | Closure | null $currency = null, int $divideBy = 0): static
    {
        $this->isMoney = true;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($currency, $divideBy): ?string {
            if (blank($state)) {
                return null;
            }

            $currency = $component->evaluate($currency) ?? Infolist::$defaultCurrency;

            return format_money($state, $currency, $divideBy);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(static function (TextEntry $component, $state) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator): ?string {
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
                $component->evaluate($decimalPlaces),
                $component->evaluate($decimalSeparator),
                $component->evaluate($thousandsSeparator),
            );
        });

        return $this;
    }

    public function time(?string $format = null, ?string $timezone = null): static
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
            }

            $state = $prefix . $state;
        }

        if (filled($suffix)) {
            if ($suffix instanceof Htmlable) {
                $suffix = $suffix->toHtml();
            }

            $state = $state . $suffix;
        }

        if ($isHtml) {
            return str($state)->sanitizeHtml()->toHtmlString();
        }

        return $state;
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

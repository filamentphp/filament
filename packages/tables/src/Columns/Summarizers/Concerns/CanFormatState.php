<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use BackedEnum;
use Closure;
use Filament\Support\Concerns\CanConfigureCommonMark;
use Filament\Support\Enums\ArgumentValue;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

trait CanFormatState
{
    use CanConfigureCommonMark;

    protected ?Closure $formatStateUsing = null;

    protected string | Closure | null $placeholder = null;

    protected string | Htmlable | Closure | null $prefix = null;

    protected string | Htmlable | Closure | null $suffix = null;

    protected bool | Closure $isHtml = false;

    protected bool | Closure $isMarkdown = false;

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function limit(int $length = 100, ?string $end = '...'): static
    {
        $this->formatStateUsing(static function ($state) use ($end, $length): ?string {
            if (blank($state)) {
                return null;
            }

            return Str::limit(strval($state), $length, $end ?? '');
        });

        return $this;
    }

    public function money(string | BackedEnum | Closure | null $currency = null, int $divideBy = 0, string | BackedEnum | Closure | null $locale = null, int | Closure | null $decimalPlaces = null): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($currency, $divideBy, $locale, $decimalPlaces): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $currency = $summarizer->evaluate($currency) ?? $summarizer->getTable()->getDefaultCurrency();
            $locale = $summarizer->evaluate($locale) ?? $summarizer->getTable()->getDefaultNumberLocale() ?? config('app.locale');
            $decimalPlaces = $summarizer->evaluate($decimalPlaces);

            if ($divideBy) {
                $state /= $divideBy;
            }

            if ($currency instanceof BackedEnum) {
                $currency = (string) $currency->value;
            }

            if ($locale instanceof BackedEnum) {
                $locale = (string) $locale->value;
            }

            return Number::currency($state, $currency, $locale, $decimalPlaces);
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null | ArgumentValue $decimalSeparator = ArgumentValue::Default, string | Closure | null | ArgumentValue $thousandsSeparator = ArgumentValue::Default, int | Closure | null $maxDecimalPlaces = null, string | Closure | null $locale = null): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($decimalPlaces, $decimalSeparator, $locale, $maxDecimalPlaces, $thousandsSeparator): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $decimalPlaces = $summarizer->evaluate($decimalPlaces);
            $decimalSeparator = $summarizer->evaluate($decimalSeparator);
            $thousandsSeparator = $summarizer->evaluate($thousandsSeparator);

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

            $locale = $summarizer->evaluate($locale) ?? $summarizer->getTable()->getDefaultNumberLocale() ?? config('app.locale');

            return Number::format($state, $decimalPlaces, $summarizer->evaluate($maxDecimalPlaces), locale: $locale);
        });

        return $this;
    }

    public function placeholder(string | Closure | null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function markdown(bool | Closure $condition = true): static
    {
        $this->isMarkdown = $condition;

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

    public function formatState(mixed $state): mixed
    {
        $isHtml = $this->isHtml();

        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

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

        if (blank($state)) {
            $state = $this->evaluate($this->placeholder);
        }

        return $isHtml ? new HtmlString($state) : $state;
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
}

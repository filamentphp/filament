<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;
use Filament\Support\Enums\ArgumentValue;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    protected string | Closure | null $placeholder = null;

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

    public function money(string | Closure | null $currency = null, int $divideBy = 0, string | Closure | null $locale = null): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($currency, $divideBy, $locale): ?string {
            if (blank($state)) {
                return null;
            }

            if (! is_numeric($state)) {
                return $state;
            }

            $currency = $summarizer->evaluate($currency) ?? Table::$defaultCurrency;

            if ($divideBy) {
                $state /= $divideBy;
            }

            return Number::currency($state, $currency, $summarizer->evaluate($locale));
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

            return Number::format($state, $decimalPlaces, $summarizer->evaluate($maxDecimalPlaces), locale: $summarizer->evaluate($locale));
        });

        return $this;
    }

    public function placeholder(string | Closure | null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function formatState(mixed $state): mixed
    {
        $state = $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);

        if (blank($state)) {
            $state = $this->evaluate($this->placeholder);
        }

        return $state;
    }
}

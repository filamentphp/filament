<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Filament\Support\format_money;
use function Filament\Support\format_number;

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
        $this->formatStateUsing(static function ($state) use ($end, $length) {
            $isArrayState = is_array($state);

            $state = array_map(function ($state) use ($end, $length) {
                if (blank($state)) {
                    return null;
                }

                return Str::limit(strval($state), $length, $end ?? '');
            }, Arr::wrap($state));

            if (! $isArrayState) {
                return $state[0];
            }

            return $state;
        });

        return $this;
    }

    public function money(string | Closure | null $currency = null, int $divideBy = 0): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($currency, $divideBy) {
            $isArrayState = is_array($state);

            $state = array_map(function ($state) use ($currency, $divideBy, $summarizer) {
                if (blank($state)) {
                    return null;
                }

                $currency = $summarizer->evaluate($currency) ?? Table::$defaultCurrency;

                return format_money($state, $currency, $divideBy);
            }, Arr::wrap($state));

            if (! $isArrayState) {
                return $state[0];
            }

            return $state;
        });

        return $this;
    }

    public function numeric(int | Closure | null $decimalPlaces = null, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator) {
            $isArrayState = is_array($state);

            $state = array_map(function ($state) use ($decimalPlaces, $decimalSeparator, $summarizer, $thousandsSeparator) {
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
                    $summarizer->evaluate($decimalPlaces),
                    $summarizer->evaluate($decimalSeparator),
                    $summarizer->evaluate($thousandsSeparator),
                );
            }, Arr::wrap($state));

            if (! $isArrayState) {
                return Arr::first($state);
            }

            return $state;
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

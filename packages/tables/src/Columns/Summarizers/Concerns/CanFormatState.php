<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Akaunting\Money;
use Closure;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

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

    public function money(string | Closure | null $currency = null, bool $shouldConvert = true): static
    {
        $this->formatStateUsing(static function ($state, Summarizer $summarizer) use ($currency, $shouldConvert) {
            $isArrayState = is_array($state);

            $state = array_map(function ($state) use ($currency, $shouldConvert, $summarizer) {
                if (blank($state)) {
                    return null;
                }

                if (blank($currency)) {
                    $currency = env('DEFAULT_CURRENCY', 'USD');
                }

                return (new Money\Money(
                    $state,
                    (new Money\Currency(strtoupper($summarizer->evaluate($currency)))),
                    $shouldConvert,
                ))->format();
            }, Arr::wrap($state));

            if (! $isArrayState) {
                return $state[0];
            }

            return $state;
        });

        return $this;
    }

    public function numeric(int | Closure $decimalPlaces = 0, string | Closure | null $decimalSeparator = '.', string | Closure | null $thousandsSeparator = ','): static
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

    public function ul(): static
    {
        $this->formatStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            if (! is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                '<ul><li>' . implode('</li><li>', $state) . '</li></ul>'
            );
        });

        return $this;
    }

    public function ol(): static
    {
        $this->formatArrayStateUsing(function ($state) {
            if (blank($state)) {
                return null;
            }

            if (! is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                '<ol><li>' . implode('</li><li>', $state) . '</li></ol>'
            );
        });

        return $this;
    }

    public function grid(int $columns = 1, $gap = 2): static
    {
        $this->formatArrayStateUsing(function ($state) use ($columns, $gap) {
            if (blank($state)) {
                return null;
            }

            if (! is_array($state)) {
                $state = [$state];
            }

            return new HtmlString(
                "<div class='grid grid-cols-{$columns} gap-{$gap}'><div>" .
                implode('</div><div>', $state) .
                '</div></div>'
            );
        });

        return $this;
    }

    public function getFormattedState(): mixed
    {
        $state = $this->getState();

        return $this->evaluate($this->formatStateUsing ?? $state, [
            'state' => $state,
        ]);
    }
}

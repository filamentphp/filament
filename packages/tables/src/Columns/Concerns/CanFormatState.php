<?php

namespace Filament\Tables\Columns\Concerns;

use Akaunting\Money;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected ?Closure $formatStateUsing = null;

    public function date(string $format = 'M j, Y'): static
    {
        $this->formatStateUsing(fn ($state): ?string => $state ? Carbon::parse($state)->translatedFormat($format) : null);

        return $this;
    }

    public function dateTime(string $format = 'M j, Y H:i:s'): static
    {
        $this->date($format);

        return $this;
    }

    public function enum(array $options, $default = null): static
    {
        $this->formatStateUsing(fn ($state): string => $options[$state] ?? ($default ?? $state));

        return $this;
    }

    public function limit(int $length = 100, string $end = '...'): static
    {
        $this->formatStateUsing(fn ($state): string => Str::limit($state, $length, $end));

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function money(string $currency = 'usd', bool $shouldConvert = false): static
    {
        $this->formatStateUsing(function ($state) use ($currency, $shouldConvert): ?string {
            if (blank($state)) {
                return null;
            }

            return (new Money\Money(
                $state,
                (new Money\Currency(strtoupper($currency))),
                $shouldConvert,
            ))->format();
        });

        return $this;
    }

    public function getFormattedState()
    {
        $state = $this->getState();

        if ($this->formatStateUsing instanceof Closure) {
            $state = app()->call($this->formatStateUsing, [
                'livewire' => $this->getLivewire(),
                'record' => $this->getRecord(),
                'state' => $state,
            ]);
        }

        return $state;
    }
}

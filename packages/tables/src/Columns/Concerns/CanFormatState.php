<?php

namespace Filament\Tables\Columns\Concerns;

use Akaunting\Money;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait CanFormatState
{
    protected $formatStateUsing = null;

    public function currency(string $currency = 'usd', bool $shouldConvert = false): static
    {
        $this->formatStateUsing(function ($state) use ($currency, $shouldConvert) {
            return (new Money\Money(
                $state,
                (new Money\Currency(strtoupper($currency))),
                $shouldConvert,
            ))->format();
        });

        return $this;
    }

    public function date(string $format = 'M j, Y'): static
    {
        $this->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat($format));

        return $this;
    }

    public function dateTime(string $format = 'M j, Y H:i:s'): static
    {
        $this->date($format);

        return $this;
    }

    public function enum(array $options): static
    {
        $this->formatStateUsing(fn ($state) => $options[$state] ?? $state);

        return $this;
    }

    public function limit(int $length = -1): static
    {
        $this->formatStateUsing(fn ($state) => Str::limit($state, $length));

        return $this;
    }

    public function formatStateUsing(callable $callback): static
    {
        $this->formatStateUsing = $callback;

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

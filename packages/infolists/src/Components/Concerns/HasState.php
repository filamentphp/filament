<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Get;
use Filament\Infolists\Set;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Livewire;

trait HasState
{
    protected ?Closure $getStateUsing = null;

    protected ?string $statePath = null;

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function hasStatePath(): bool
    {
        return filled($this->statePath);
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && ($containerStatePath = $this->getContainer()->getStatePath())) {
            $pathComponents[] = $containerStatePath;
        }

        if ($this->hasStatePath()) {
            $pathComponents[] = $this->statePath;
        }

        return implode('.', $pathComponents);
    }

    public function getState(): mixed
    {
        if ($this->getStateUsing) {
            return $this->evaluate(
                $this->getStateUsing,
                exceptParameters: ['state'],
            );
        }

        return data_get($this->getRecord(), $this->getStatePath());
    }
}

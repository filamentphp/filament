<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Contracts\HasForms;

trait BelongsToLivewire
{
    protected HasForms $livewire;

    protected ?string $livewireKey = null;

    public function livewire(HasForms $livewire): static
    {
        $this->livewire = $livewire;

        return $this;
    }

    public function livewireKey(?string $key): static
    {
        $this->livewireKey = $key;

        return $this;
    }

    public function getLivewire(): HasForms
    {
        return $this->livewire;
    }

    public function getLivewireKey(): ?string
    {
        $keyComponents = [$this->getLivewire()->id];

        if ($parentComponentLivewireKey = $this->getParentComponent()?->getContainer()->getLivewireKey()) {
            $keyComponents[] = $parentComponentLivewireKey;
        }

        if ($this->livewireKey !== null) {
            $keyComponents[] = $this->livewireKey;
        }

        return implode('.', $keyComponents);
    }
}

<?php

namespace Filament\Forms\Concerns;

trait HasContext
{
    protected ?string $context = null;

    public function context(?string $context = null): static
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): string
    {
        if (filled($this->context)) {
            return $this->context;
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getContainer()->getContext();
        }

        return $this->getLivewire()::class;
    }
}

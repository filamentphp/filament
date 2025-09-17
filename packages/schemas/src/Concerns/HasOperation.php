<?php

namespace Filament\Schemas\Concerns;

trait HasOperation
{
    protected ?string $operation = null;

    public function operation(?string $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @deprecated Use `operation()` instead.
     */
    public function context(?string $context): static
    {
        $this->operation($context);

        return $this;
    }

    public function getOperation(): string
    {
        if (filled($this->operation)) {
            return $this->operation;
        }

        if ($this->getParentComponent()) {
            return $this->getParentComponent()->getContainer()->getOperation();
        }

        return $this->getLivewire()::class;
    }
}

<?php

namespace Filament\Tables\Columns\Concerns;

trait HasAlignment
{
    protected ?string $alignment = null;

    public function alignment(?string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }
}

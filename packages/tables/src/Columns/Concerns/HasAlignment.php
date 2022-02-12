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

    public function alignLeft(): static
    {
        return $this->alignment('left');
    }

    public function alignCenter(): static
    {
        return $this->alignment('center');
    }

    public function alignRight(): static
    {
        return $this->alignment('right');
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }
}

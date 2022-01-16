<?php

namespace Filament\Pages\Actions\Modal\Actions\Concerns;

trait HasColor
{
    protected ?string $color = null;

    public function color(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}

<?php

namespace Filament\Panel\Concerns;

trait HasBrandName
{
    protected ?string $brandName = null;

    public function brandName(?string $name): static
    {
        $this->brandName = $name;

        return $this;
    }

    public function getBrandName(): string
    {
        return $this->brandName ?? config('app.name');
    }
}

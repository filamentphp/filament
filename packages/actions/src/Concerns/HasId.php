<?php

namespace Filament\Actions\Concerns;

trait HasId
{
    protected ?string $id = null;

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id ?? $this->getName();
    }
}

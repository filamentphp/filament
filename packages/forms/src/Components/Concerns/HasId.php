<?php

namespace Filament\Forms\Components\Concerns;

trait HasId
{
    protected $id = null;

    public function id(string | callable $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->evaluate($this->id);
    }
}

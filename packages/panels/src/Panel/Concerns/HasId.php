<?php

namespace Filament\Panel\Concerns;

use Exception;

trait HasId
{
    protected string $id;

    public function id(string $id): static
    {
        if (isset($this->id)) {
            throw new Exception("The panel has already been registered with the ID [{$this->id}].");
        }

        $this->id = $id;
        $this->configure();
        $this->restoreCachedComponents();

        return $this;
    }

    public function getId(): string
    {
        if (! isset($this->id)) {
            throw new Exception('A panel has been registered without an `id()`.');
        }

        return $this->id;
    }
}

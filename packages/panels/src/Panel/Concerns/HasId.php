<?php

namespace Filament\Panel\Concerns;

use LogicException;

trait HasId
{
    protected string $id;

    public function id(string $id): static
    {
        if (isset($this->id)) {
            // Return existing instance instead of throwing exception
            if ($this->id === $id) {
                return $this;
            }

            throw new LogicException("The panel has already been registered with the ID [{$this->id}]. Cannot change to [{$id}].");
        }

        if (empty(trim($id))) {
            throw new LogicException('Panel ID cannot be empty.');
        }

        $this->id = $id;
        $this->configure();
        $this->restoreCachedComponents();

        return $this;
    }

    public function getId(): string
    {
        if (! isset($this->id)) {
            throw new LogicException('A panel has been registered without an `id()`. Please call the `id()` method before using the panel.');
        }

        return $this->id;
    }
}

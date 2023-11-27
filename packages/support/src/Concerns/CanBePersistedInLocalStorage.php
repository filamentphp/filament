<?php

namespace Filament\Support\Concerns;

trait CanBePersistedInLocalStorage
{
    protected bool | Closure | null $persistTabInLocalStorage = false;

    public ?string $tablocalStorageName = null;

    public function persistTabInLocalStorage(Closure | bool $value = true): static
    {
        $this->persistTabInLocalStorage = $value;

        return $this;
    }

    public function tabLocalStorageName(string $name): static
    {
        $this->tablocalStorageName = $name;

        return $this;
    }

    public function getTabLocalStorageName(): string
    {
        return str_slug(class_basename($this->getLivewire()).'-'.$this->getContainer()->getRecord()->getTable().'-'. ($this->tablocalStorageName ?? $this->getContainer()->getRecord()->getKey()));
    }

    public function isTabPersistedInLocalStorage(): bool
    {
        return $this->evaluate($this->persistTabInLocalStorage);
    }
}

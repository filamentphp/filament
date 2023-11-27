<?php

namespace Filament\Support\Concerns;

trait CanBePersistedInLocalStorage
{
    protected bool | Closure | null $persistTabInLocalStorage = false;

    public function persistTabInLocalStorage(Closure | bool $value = true): static
    {
        $this->persistTabInLocalStorage = $value;

        return $this;
    }

    public function getTabLocalStorageName(): string
    {
        return str_slug(class_basename($this->getLivewire()).'-'.$this->getModelInstance()->getTable().'-'.$this->getModelInstance()->getKey().'-activeTab');
    }

    public function isTabPersistedInLocalStorage(): bool
    {
        return $this->evaluate($this->persistTabInLocalStorage);
    }
}

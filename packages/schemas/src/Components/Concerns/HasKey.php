<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait HasKey
{
    protected string | Closure | null $key = null;

    protected bool $isKeyInheritable = true;

    protected ?string $cachedAbsoluteKey = null;

    protected bool $hasCachedAbsoluteKey = false;

    protected ?string $cachedAbsoluteInheritanceKey = null;

    protected bool $hasCachedAbsoluteInheritanceKey = false;

    public function key(string | Closure | null $key, bool $isInheritable = true): static
    {
        $this->key = $key;
        $this->isKeyInheritable = $isInheritable;

        return $this;
    }

    public function getKey(bool $isAbsolute = true): ?string
    {
        if ($isAbsolute && $this->hasCachedAbsoluteKey) {
            return $this->cachedAbsoluteKey;
        }

        $key = ($this->isKeyInheritable() || (! $this->hasStatePath()))
            ? ($this->evaluate($this->key) ?? $this->getStatePath(isAbsolute: false))
            : $this->getStatePath(isAbsolute: false);

        if (! $isAbsolute) {
            return $key;
        }

        if (blank($key)) {
            return $this->cacheAbsoluteKey(null);
        }

        $keyComponents = [];

        if (filled($containerInheritanceKey = $this->getContainer()->getInheritanceKey())) {
            $keyComponents[] = $containerInheritanceKey;
        }

        $keyComponents[] = $key;

        return $this->cacheAbsoluteKey(implode('.', $keyComponents));
    }

    public function getInheritanceKey(bool $isAbsolute = true): ?string
    {
        if ($isAbsolute && $this->hasCachedAbsoluteInheritanceKey) {
            return $this->cachedAbsoluteInheritanceKey;
        }

        if (! $isAbsolute) {
            return $this->isKeyInheritable() ? $this->getKey(isAbsolute: false) : $this->getStatePath(isAbsolute: false);
        }

        if ($this->isKeyInheritable()) {
            $key = $this->getKey();

            if (filled($key)) {
                return $this->cacheAbsoluteInheritanceKey($key);
            }
        } elseif ($this->hasStatePath()) {
            $keyComponents = [];

            if (filled($containerInheritanceKey = $this->getContainer()->getInheritanceKey())) {
                $keyComponents[] = $containerInheritanceKey;
            }

            $keyComponents[] = $this->getStatePath(isAbsolute: false);

            return $this->cacheAbsoluteInheritanceKey(implode('.', $keyComponents));
        }

        return $this->cacheAbsoluteInheritanceKey($this->getContainer()->getInheritanceKey());
    }

    public function isKeyInheritable(): bool
    {
        return $this->isKeyInheritable;
    }

    protected function cacheAbsoluteKey(?string $key): ?string
    {
        try {
            return $this->cachedAbsoluteKey = $key;
        } finally {
            $this->hasCachedAbsoluteKey = true;
        }
    }

    protected function cacheAbsoluteInheritanceKey(?string $key): ?string
    {
        try {
            return $this->cachedAbsoluteInheritanceKey = $key;
        } finally {
            $this->hasCachedAbsoluteInheritanceKey = true;
        }
    }

    protected function flushCachedAbsoluteKey(): void
    {
        $this->cachedAbsoluteKey = null;
        $this->hasCachedAbsoluteKey = false;
    }

    protected function flushCachedAbsoluteInheritanceKey(): void
    {
        $this->cachedAbsoluteInheritanceKey = null;
        $this->hasCachedAbsoluteInheritanceKey = false;
    }

    public function getLivewireKey(): ?string
    {
        $key = $this->getKey();

        if (blank($key)) {
            return null;
        }

        return "{$this->getLivewire()->getId()}.{$key}";
    }
}

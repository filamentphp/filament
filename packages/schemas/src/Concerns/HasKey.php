<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait HasKey
{
    protected string | Closure | null $key = null;

    protected ?string $cachedAbsoluteKey = null;

    protected bool $hasCachedAbsoluteKey = false;

    protected ?string $cachedInheritanceKey = null;

    protected bool $hasCachedInheritanceKey = false;

    public function key(string | Closure | null $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(bool $isAbsolute = true): ?string
    {
        if ($isAbsolute && $this->hasCachedAbsoluteKey) {
            return $this->cachedAbsoluteKey;
        }

        $key = $this->evaluate($this->key) ?? $this->getStatePath(isAbsolute: false);

        if (! $isAbsolute) {
            return $key;
        }

        $keyComponents = [];

        if (filled($parentComponentInheritanceKey = $this->getParentComponent()?->getInheritanceKey())) {
            $keyComponents[] = $parentComponentInheritanceKey;
        }

        if (filled($key)) {
            $keyComponents[] = $key;
        }

        return $this->cacheAbsoluteKey(implode('.', $keyComponents));
    }

    public function getInheritanceKey(): ?string
    {
        if ($this->hasCachedInheritanceKey) {
            return $this->cachedInheritanceKey;
        }

        $key = $this->getKey();

        if (filled($key)) {
            return $this->cacheInheritanceKey($key);
        }

        return $this->cacheInheritanceKey($this->getParentComponent()?->getInheritanceKey());
    }

    protected function cacheAbsoluteKey(?string $key): ?string
    {
        try {
            return $this->cachedAbsoluteKey = $key;
        } finally {
            $this->hasCachedAbsoluteKey = true;
        }
    }

    protected function cacheInheritanceKey(?string $key): ?string
    {
        try {
            return $this->cachedInheritanceKey = $key;
        } finally {
            $this->hasCachedInheritanceKey = true;
        }
    }

    protected function flushCachedAbsoluteKey(): void
    {
        $this->cachedAbsoluteKey = null;
        $this->hasCachedAbsoluteKey = false;
    }

    protected function flushCachedInheritanceKey(): void
    {
        $this->cachedInheritanceKey = null;
        $this->hasCachedInheritanceKey = false;
    }

    public function getLivewireKey(): ?string
    {
        $key = $this->getKey();

        if (blank($key)) {
            return null;
        }

        return "{$this->getLivewire()->getId()}.{$key}";
    }

    /**
     * @deprecated Use `key()` instead.
     */
    public function name(string | Closure | null $name): static
    {
        $this->key($name);

        return $this;
    }

    /**
     * @deprecated Use `getKey()` instead.
     */
    public function getName(): ?string
    {
        return $this->getKey();
    }
}

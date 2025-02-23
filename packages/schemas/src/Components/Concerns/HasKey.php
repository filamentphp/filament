<?php

namespace Filament\Schemas\Components\Concerns;

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

    public function getInheritanceKey(): ?string
    {
        if ($this->hasCachedInheritanceKey) {
            return $this->cachedInheritanceKey;
        }

        $key = $this->getKey();

        if (filled($key)) {
            return $this->cacheInheritanceKey($key);
        }

        return $this->cacheInheritanceKey($this->getContainer()->getInheritanceKey());
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
}

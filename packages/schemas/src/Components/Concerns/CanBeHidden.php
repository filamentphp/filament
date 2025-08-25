<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Arr;
use Livewire\Component as LivewireComponent;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    protected string | Closure | null $visibleJs = null;

    protected string | Closure | null $hiddenJs = null;

    protected static bool $isCachingVisibility = false;

    /**
     * @var array<string, bool>
     */
    protected static array $visibilityCache = [];

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * @param  string | Operation | array<string | Operation>  $operations
     */
    public function hiddenOn(string | Operation | array $operations): static
    {
        $this->hidden(static function (LivewireComponent & HasSchemas $livewire, string $operation) use ($operations): bool {
            foreach (Arr::wrap($operations) as $hiddenOperation) {
                if ($hiddenOperation instanceof Operation) {
                    $hiddenOperation = $hiddenOperation->value;
                }

                if ($hiddenOperation === $operation || $livewire instanceof $hiddenOperation) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function hiddenWhenAllChildComponentsHidden(): static
    {
        $this->hidden(static function (Component $component): bool {
            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                foreach ($childSchema->getComponents(withHidden: false) as $childComponent) {
                    return false;
                }
            }

            return true;
        });

        return $this;
    }

    /**
     * @param  string | array<string>  $paths
     */
    public function whenTruthy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Get $get) use ($paths): bool {
            foreach ($paths as $path) {
                if (! $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    /**
     * @param  string | array<string>  $paths
     */
    public function whenFalsy(string | array $paths): static
    {
        $paths = Arr::wrap($paths);

        $this->hidden(static function (Get $get) use ($paths): bool {
            foreach ($paths as $path) {
                if ((bool) $get($path)) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    /**
     * @param  string | Operation | array<string | Operation>  $operations
     */
    public function visibleOn(string | Operation | array $operations): static
    {
        $this->visible(static function (LivewireComponent & HasSchemas $livewire, string $operation) use ($operations): bool {
            foreach (Arr::wrap($operations) as $visibleOperation) {
                if ($visibleOperation instanceof Operation) {
                    $visibleOperation = $visibleOperation->value;
                }

                if ($visibleOperation === $operation || $livewire instanceof $visibleOperation) {
                    return true;
                }
            }

            return false;
        });

        return $this;
    }

    public function isHidden(): bool
    {
        if (static::isVisibilityCacheEnabled()) {
            $componentKey = $this->getKey() ?? spl_object_id($this);

            if ($this->isHidden instanceof Closure) {
                $hiddenClosureKey = $componentKey . '.hidden.' . spl_object_id($this->isHidden);

                if (! static::hasVisibilityCacheKey($hiddenClosureKey)) {
                    static::setVisibilityCacheValue(
                        $hiddenClosureKey,
                        ! $this->evaluate($this->isHidden),
                    );
                }

                if (! static::getVisibilityCacheValue($hiddenClosureKey)) {
                    return true;
                }
            } elseif ($this->isHidden) {
                return true;
            }

            if ($this->isVisible instanceof Closure) {
                $visibleClosureKey = $componentKey . '.visible.' . spl_object_id($this->isVisible);

                if (! static::hasVisibilityCacheKey($visibleClosureKey)) {
                    static::setVisibilityCacheValue(
                        $visibleClosureKey,
                        (bool) $this->evaluate($this->isVisible)
                    );
                }

                return ! static::getVisibilityCacheValue($visibleClosureKey);
            }

            return ! $this->isVisible;
        }

        if ($this->evaluate($this->isHidden)) {
            return true;
        }

        return ! $this->evaluate($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }

    public function visibleJs(string | Closure | null $condition): static
    {
        $this->visibleJs = $condition;

        return $this;
    }

    public function hiddenJs(string | Closure | null $condition): static
    {
        $this->hiddenJs = $condition;

        return $this;
    }

    public function getVisibleJs(): ?string
    {
        return $this->evaluate($this->visibleJs);
    }

    public function getHiddenJs(): ?string
    {
        return $this->evaluate($this->hiddenJs);
    }

    public static function isVisibilityCacheEnabled(): bool
    {
        return static::$isCachingVisibility;
    }

    public static function getVisibilityCacheValue(string $key): bool
    {
        return static::$visibilityCache[$key] ?? false;
    }

    public static function setVisibilityCacheValue(string $key, bool $value): void
    {
        static::$visibilityCache[$key] = $value;
    }

    public static function hasVisibilityCacheKey(string $key): bool
    {
        return array_key_exists($key, static::$visibilityCache);
    }

    public static function enableVisibilityCache(): void
    {
        static::$isCachingVisibility = true;
        static::$visibilityCache = [];
    }

    public static function disableVisibilityCache(): void
    {
        static::$isCachingVisibility = false;
        static::$visibilityCache = [];
    }

    /**
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function withVisibilityCache(callable $callback): mixed
    {
        $wasEnabled = static::isVisibilityCacheEnabled();

        if (! $wasEnabled) {
            static::enableVisibilityCache();
        }

        try {
            return $callback();
        } finally {
            if (! $wasEnabled) {
                static::disableVisibilityCache();
            }
        }
    }
}

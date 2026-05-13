<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;

trait CanLazyLoadItems
{
    protected bool | Closure $isLazy = false;

    protected bool | Closure $shouldUnloadOnCollapse = false;

    public function lazy(bool | Closure $condition = true, bool | Closure $unloadOnCollapse = false): static
    {
        $this->isLazy = $condition;
        $this->shouldUnloadOnCollapse = $unloadOnCollapse;

        // `lazy()` implies collapsible + initially-collapsed — content that is always
        // visible can never be lazy-loaded. We only skip the implicit setup when the
        // caller passes the literal `false`, because a `Closure` may still evaluate to
        // `true` at render time and we cannot know at configure-time.
        if ($condition !== false) {
            $this->collapsed();
        }

        return $this;
    }

    public function isLazy(?Schema $item = null): bool
    {
        if ($item) {
            $block = $item->getParentComponent();

            if ($block instanceof Block) {
                $blockIsLazy = $block->getIsLazy();

                if ($blockIsLazy !== null) {
                    return $blockIsLazy;
                }
            }
        }

        return (bool) $this->evaluate($this->isLazy, ['item' => $item]);
    }

    public function shouldUnloadOnCollapse(?Schema $item = null): bool
    {
        if (! $this->isLazy($item)) {
            return false;
        }

        if ($item) {
            $block = $item->getParentComponent();

            if ($block instanceof Block) {
                $blockShouldUnload = $block->getShouldUnloadOnCollapse();

                if ($blockShouldUnload !== null) {
                    return $blockShouldUnload;
                }
            }
        }

        return (bool) $this->evaluate($this->shouldUnloadOnCollapse, ['item' => $item]);
    }

    public function isItemLoaded(string $itemKey): bool
    {
        $loaded = data_get($this->getLivewire(), $this->getLoadedItemsPath());

        return is_array($loaded) && in_array($itemKey, $loaded, true);
    }

    public function itemHasErrors(string $itemKey): bool
    {
        $errorBag = $this->getLivewire()->getErrorBag();

        if (! $errorBag->any()) {
            return false;
        }

        $prefix = $this->getStatePath() . '.' . $itemKey . '.';

        foreach ($errorBag->keys() as $errorKey) {
            if (str_starts_with($errorKey, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function loadItemsWithErrors(): void
    {
        if (! $this->isLazy()) {
            return;
        }

        $errorBag = $this->getLivewire()->getErrorBag();

        if (! $errorBag->any()) {
            return;
        }

        $prefix = $this->getStatePath() . '.';
        $itemKeys = [];

        foreach ($errorBag->keys() as $errorKey) {
            if (! str_starts_with($errorKey, $prefix)) {
                continue;
            }

            $remainder = substr($errorKey, strlen($prefix));
            $separatorPosition = strpos($remainder, '.');

            $itemKey = $separatorPosition === false ? $remainder : substr($remainder, 0, $separatorPosition);

            if ($itemKey !== '') {
                $itemKeys[] = $itemKey;
            }
        }

        if ($itemKeys === []) {
            return;
        }

        $livewire = $this->getLivewire();
        $path = $this->getLoadedItemsPath();

        $loaded = data_get($livewire, $path);
        $loaded = is_array($loaded) ? $loaded : [];
        $loaded = array_values(array_unique(array_merge($loaded, $itemKeys)));

        data_set($livewire, $path, $loaded);
    }

    #[ExposedLivewireMethod]
    public function loadItem(string $itemKey): void
    {
        $livewire = $this->getLivewire();
        $path = $this->getLoadedItemsPath();

        $loaded = data_get($livewire, $path);
        $loaded = is_array($loaded) ? $loaded : [];

        if (! in_array($itemKey, $loaded, true)) {
            $loaded[] = $itemKey;
            data_set($livewire, $path, $loaded);
        }
    }

    #[ExposedLivewireMethod]
    public function unloadItem(string $itemKey): void
    {
        $livewire = $this->getLivewire();
        $path = $this->getLoadedItemsPath();

        $loaded = data_get($livewire, $path);
        $loaded = is_array($loaded) ? $loaded : [];

        data_set($livewire, $path, array_values(array_filter(
            $loaded,
            static fn (string $loadedKey): bool => $loadedKey !== $itemKey,
        )));
    }

    private function getLoadedItemsPath(): string
    {
        return 'loadedSchemaComponentItems.' . addcslashes((string) $this->getKey(), '.');
    }
}

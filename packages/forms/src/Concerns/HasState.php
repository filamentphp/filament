<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\BaseFileUpload;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasState
{
    protected ?string $statePath = null;

    public function callAfterStateHydrated(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->callAfterStateHydrated();

            foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                $container->callAfterStateHydrated();
            }
        }
    }

    public function callAfterStateUpdated(string $path): bool
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->getStatePath() === $path) {
                $component->callAfterStateUpdated();

                return true;
            }

            if ($component instanceof BaseFileUpload && Str::of($path)->startsWith("{$component->getStatePath()}.")) {
                $component->callAfterStateUpdated();

                return true;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->callAfterStateUpdated($path)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function callBeforeStateDehydrated(): void
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->isHidden()) {
                continue;
            }

            $component->callBeforeStateDehydrated();

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->callBeforeStateDehydrated();
            }
        }
    }

    public function dehydrateState(array &$state = []): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component->isHidden()) {
                continue;
            }

            $componentStatePath = $component->getStatePath();

            if ($component->isDehydrated()) {
                if ($component->getStatePath(isAbsolute: false)) {
                    data_set($state, $componentStatePath, $component->dehydrateState());
                }

                if (
                    $component instanceof BaseFileUpload &&
                    ($componentFileNamesStatePath = $component->getFileNamesStatePath())
                ) {
                    data_set($state, $componentFileNamesStatePath, $component->dehydrateStoredFileNames());
                }

                foreach ($component->getChildComponentContainers() as $container) {
                    if ($container->isHidden()) {
                        continue;
                    }

                    $container->dehydrateState($state);
                }
            } else {
                Arr::forget($state, $componentStatePath);
            }
        }

        return $state;
    }

    public function mutateDehydratedState(array &$state = []): array
    {
        foreach ($this->getComponents() as $component) {
            if ($component->isHidden()) {
                continue;
            }

            if (! $component->isDehydrated()) {
                continue;
            }

            foreach ($component->getChildComponentContainers() as $container) {
                if ($container->isHidden()) {
                    continue;
                }

                $container->mutateDehydratedState($state);
            }

            if ($component->getStatePath(isAbsolute: false)) {
                if (! $component->mutatesDehydratedState()) {
                    continue;
                }

                $componentStatePath = $component->getStatePath();

                data_set(
                    $state,
                    $componentStatePath,
                    $component->mutateDehydratedState(
                        data_get($state, $componentStatePath),
                    ),
                );
            }
        }

        return $state;
    }

    public function fill(?array $state = null): static
    {
        if ($state === null) {
            return $this->fillWithDefaults();
        }

        $livewire = $this->getLivewire();

        if ($statePath = $this->getStatePath()) {
            data_set($livewire, $statePath, $state);
        } else {
            $this->fillComponentStateWithNull();

            foreach ($state as $key => $value) {
                data_set($livewire, $key, $value);
            }
        }

        $this->loadStateFromRelationships();

        $this->callAfterStateHydrated();

        $this->fillComponentStateWithNull(shouldOverwrite: false);

        return $this;
    }

    protected function fillWithDefaults(): static
    {
        if ($statePath = $this->getStatePath()) {
            $livewire = $this->getLivewire();

            data_set($livewire, $statePath, []);
        } else {
            $this->fillComponentStateWithNull();
        }

        $this->hydrateDefaultState();

        $this->fillComponentStateWithNull(shouldOverwrite: false);

        return $this;
    }

    public function hydrateDefaultState(): static
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            $component->hydrateDefaultState();
            $component->callAfterStateHydrated();

            foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                $container->hydrateDefaultState();
            }
        }

        return $this;
    }

    public function fillComponentStateWithNull(bool $shouldOverwrite = true): static
    {
        foreach ($this->getComponents(withHidden: true) as $component) {
            if ($component->hasChildComponentContainer(withHidden: true)) {
                foreach ($component->getChildComponentContainers(withHidden: true) as $container) {
                    $container->fillComponentStateWithNull($shouldOverwrite);
                }
            } else {
                $component->fillStateWithNull($shouldOverwrite);
            }
        }

        return $this;
    }

    public function statePath(?string $path): static
    {
        $this->statePath = $path;

        return $this;
    }

    public function getState(bool $shouldCallHooksBefore = true): array
    {
        $state = $this->validate();

        if ($shouldCallHooksBefore) {
            $this->callBeforeStateDehydrated();
            $this->saveRelationships();
            $this->loadStateFromRelationships();
        }

        $this->dehydrateState($state);
        $this->mutateDehydratedState($state);

        if ($statePath = $this->getStatePath()) {
            return data_get($state, $statePath) ?? [];
        }

        return $state;
    }

    public function getRawState(): array
    {
        return data_get($this->getLivewire(), $this->getStatePath()) ?? [];
    }

    public function getStateOnly(array $keys): array
    {
        return Arr::only($this->getState(), $keys);
    }

    public function getStateExcept(array $keys): array
    {
        return Arr::except($this->getState(), $keys);
    }

    public function getStatePath(bool $isAbsolute = true): string
    {
        $pathComponents = [];

        if ($isAbsolute && $parentComponentStatePath = $this->getParentComponent()?->getStatePath()) {
            $pathComponents[] = $parentComponentStatePath;
        }

        if (($statePath = $this->statePath) !== null) {
            $pathComponents[] = $statePath;
        }

        return implode('.', $pathComponents);
    }
}

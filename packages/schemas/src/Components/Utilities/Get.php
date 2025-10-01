<?php

namespace Filament\Schemas\Components\Utilities;

use Filament\Schemas\Components\Component;

class Get
{
    /**
     * @var array<string, Component | null>
     */
    protected static array $componentCache = [];

    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $path = $this->component->resolveRelativeStatePath($path, $isAbsolute);

        // Early return if path refers to self
        if ($path === $this->component->getStatePath()) {
            return $this->component->getState();
        }

        $cacheKey = spl_object_id($this->component->getRootContainer()) . ':' . $path;

        // Check cache first
        if (! array_key_exists($cacheKey, self::$componentCache)) {
            self::$componentCache[$cacheKey] = $this->component->getRootContainer()->getComponentByStatePath(
                $path,
                withHidden: true,
                withAbsoluteStatePath: true,
                skipComponentChildContainersWhileSearching: $this->component,
            );
        }

        $component = self::$componentCache[$cacheKey];

        if (! $component) {
            return data_get($livewire, $path);
        }

        return $component->getState();
    }
}

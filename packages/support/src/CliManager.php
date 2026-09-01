<?php

namespace Filament\Support;

class CliManager
{
    /**
     * @var array<string, array{path: string, viewNamespace: ?string}>
     */
    protected array $componentLocations = [];

    /**
     * @var array<string, array{path: string, viewNamespace: ?string}>
     */
    protected array $livewireComponentLocations = [];

    public function registerComponentLocation(string $path, string $namespace, ?string $viewNamespace): void
    {
        $this->componentLocations[$namespace] = [
            'path' => $path,
            'viewNamespace' => $viewNamespace,
        ];
    }

    /**
     * @return array<string, array{path: string, viewNamespace: ?string}>
     */
    public function getComponentLocations(): array
    {
        return $this->componentLocations;
    }

    public function registerLivewireComponentLocation(string $path, string $namespace, ?string $viewNamespace): void
    {
        $this->livewireComponentLocations[$namespace] = [
            'path' => $path,
            'viewNamespace' => $viewNamespace,
        ];
    }

    /**
     * @return array<string, array{path: string, viewNamespace: ?string}>
     */
    public function getLivewireComponentLocations(): array
    {
        return $this->livewireComponentLocations;
    }
}

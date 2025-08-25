<?php

namespace Filament;

use Filament\Exceptions\NoDefaultPanelSetException;
use Illuminate\Support\Arr;

class PanelRegistry
{
    /**
     * @var array<string, Panel>
     */
    public array $panels = [];

    public ?Panel $defaultPanel = null;

    public function register(Panel $panel): void
    {
        $this->panels[$panel->getId()] = $panel;

        $panel->register();
    }

    /**
     * @throws NoDefaultPanelSetException
     */
    public function getDefault(): Panel
    {
        return $this->defaultPanel ??= Arr::first(
            $this->panels,
            fn (Panel $panel): bool => $panel->isDefault(),
            fn () => throw new NoDefaultPanelSetException('No default Filament panel is set. You may do this with the `default()` method inside a Filament provider\'s `panel()` configuration.'),
        );
    }

    public function get(?string $id = null, bool $isStrict = true): ?Panel
    {
        if ($id === null) {
            return null;
        }

        if ($isStrict) {
            return $this->panels[$id] ?? null;
        }

        $normalize = fn (string $panelId): string => (string) str($panelId)
            ->lower()
            ->replace(['-', '_'], '');

        $panels = [];

        foreach ($this->panels as $key => $panel) {
            $panels[$normalize($key)] = $panel;
        }

        return $panels[$normalize($id)] ?? null;
    }

    /**
     * @return array<string, Panel>
     */
    public function all(): array
    {
        return $this->panels;
    }
}

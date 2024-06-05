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

    public function register(Panel $panel): void
    {
        $this->panels[$panel->getId()] = $panel;

        $panel->register();

        if (! $panel->isDefault()) {
            return;
        }

        if (app()->resolved('filament')) {
            app('filament')->setCurrentPanel($panel);
        }

        app()->resolving(
            'filament',
            fn (FilamentManager $manager) => $manager->setCurrentPanel($panel),
        );
    }

    /**
     * @throws NoDefaultPanelSetException
     */
    public function getDefault(): Panel
    {
        return Arr::first(
            $this->panels,
            fn (Panel $panel): bool => $panel->isDefault(),
            fn () => throw NoDefaultPanelSetException::make(),
        );
    }

    /**
     * @throws NoDefaultPanelSetException
     */
    public function get(?string $id = null, bool $isStrict = true): Panel
    {
        return $this->findPanel($id, $isStrict) ?? $this->getDefault();
    }

    protected function findPanel(?string $id = null, bool $isStrict = true): ?Panel
    {
        if ($id === null) {
            return null;
        }

        if ($isStrict) {
            return $this->panels[$id] ?? null;
        }

        $sanitizedPanels = [];
        foreach ($this->panels as $key => $panel) {
            $sanitizedKey = strtolower(str_replace(['-', '_'], '', $key));
            $sanitizedPanels[$sanitizedKey] = $panel;
        }

        $sanitizedId = strtolower(str_replace(['-', '_'], '', $id));

        return $sanitizedPanels[$sanitizedId] ?? null;
    }

    /**
     * @return array<string, Panel>
     */
    public function all(): array
    {
        return $this->panels;
    }
}

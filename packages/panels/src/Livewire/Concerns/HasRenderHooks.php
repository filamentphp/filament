<?php

namespace Filament\Livewire\Concerns;

use Filament\Support\Facades\FilamentView;

trait HasRenderHooks
{
    public function bootHasRenderHooks(): void
    {
        foreach ($this->getRenderHooks() as $name => $hook) {
            FilamentView::registerRenderHook($name, $hook, static::class);
        }
    }

    /**
     * @return array<string, callable(): \Illuminate\Contracts\View\View|string>
     */
    protected function getRenderHooks(): array
    {
        return [];
    }
}

<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

trait HasRenderHooks
{
    /**
     * @var array<string, array<Closure>>
     */
    protected array $renderHooks = [];

    public function renderHook(string $name, Closure $hook): static
    {
        $this->renderHooks[$name][] = $hook;

        return $this;
    }

    protected function registerRenderHooks(): void
    {
        foreach ($this->renderHooks as $hookName => $hooks) {
            foreach ($hooks as $hook) {
                FilamentView::registerRenderHook($hookName, $hook);
            }
        }
    }
}

<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;

trait HasRenderHooks
{
    /**
     * @var array<string, array<string, array<Closure>>>
     */
    protected array $renderHooks = [];

    /**
     * @param  string | array<string> | null  $scopes
     */
    public function renderHook(string $name, Closure $hook, string | array | null $scopes = null): static
    {
        if (! is_array($scopes)) {
            $scopes = [$scopes];
        }

        foreach ($scopes as $scopeName) {
            $this->renderHooks[$name][$scopeName][] = $hook;
        }

        return $this;
    }

    protected function registerRenderHooks(): void
    {
        foreach ($this->renderHooks as $hookName => $scopedHooks) {
            foreach ($scopedHooks as $scope => $hooks) {
                foreach ($hooks as $hook) {
                    FilamentView::registerRenderHook($hookName, $hook, $scope);
                }
            }
        }
    }
}

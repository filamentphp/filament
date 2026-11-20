<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;

trait HasRenderHooks
{
    /**
     * @var array<string, array<array{hook: Closure, scopes: array<string>}>>
     */
    protected array $renderHooks = [];

    /**
     * @param  string | array<string> | null  $scopes
     */
    public function renderHook(string $name, Closure $hook, string | array | null $scopes = null): static
    {
        if ($scopes === null) {
            $scopes = [''];
        }

        if (! is_array($scopes)) {
            $scopes = [$scopes];
        }

        $this->renderHooks[$name][] = ['hook' => $hook, 'scopes' => $scopes];

        return $this;
    }

    protected function registerRenderHooks(): void
    {
        foreach ($this->renderHooks as $hookName => $hooks) {
            foreach ($hooks as $hook) {
                FilamentView::registerRenderHook($hookName, $hook['hook'], $hook['scopes']);
            }
        }
    }
}

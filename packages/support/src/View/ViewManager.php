<?php

namespace Filament\Support\View;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewManager
{
    /**
     * @var array<string, array<string, array<Closure>>>
     */
    protected array $renderHooks = [];

    public function registerRenderHook(string $name, Closure $hook, ?string $scope = null): void
    {
        $this->renderHooks[$name][$scope][] = $hook;
    }

    public function renderHook(string $name, ?string $scope = null): Htmlable
    {
        $renderHook = fn (callable $hook): string => (string) app()->call($hook);

        $hooks = array_map(
            $renderHook,
            $this->renderHooks[$name][null] ?? [],
        );

        if (filled($scope)) {
            $hooks = [
                ...$hooks,
                ...array_map(
                    $renderHook,
                    $this->renderHooks[$name][$scope] ?? [],
                ),
            ];
        }

        return new HtmlString(implode('', $hooks));
    }
}

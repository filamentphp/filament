<?php

namespace Filament\Support\View;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewManager
{
    /**
     * @var array<string, array<Closure>>
     */
    protected array $renderHooks = [];

    public function registerRenderHook(string $name, Closure $hook): void
    {
        $this->renderHooks[$name][] = $hook;
    }

    public function renderHook(string $name): Htmlable
    {
        $hooks = array_map(
            fn (callable $hook): string => (string) app()->call($hook),
            $this->renderHooks[$name] ?? [],
        );

        return new HtmlString(implode('', $hooks));
    }
}

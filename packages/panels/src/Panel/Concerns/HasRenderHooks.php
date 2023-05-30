<?php

namespace Filament\Panel\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

trait HasRenderHooks
{
    /**
     * @var array<string, array<Closure>>
     */
    protected array $renderHooks = [];

    public function renderHook(string $name, Closure $callback): static
    {
        $this->renderHooks[$name][] = $callback;

        return $this;
    }

    public function getRenderHook(string $name): Htmlable
    {
        $hooks = array_map(
            fn (callable $hook): string => (string) app()->call($hook),
            $this->renderHooks[$name] ?? [],
        );

        return new HtmlString(implode('', $hooks));
    }
}

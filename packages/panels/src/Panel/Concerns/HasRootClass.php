<?php

namespace Filament\Panel\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;

trait HasRootClass
{
    protected string | Htmlable | Closure | null $rootClass = null;

    public function rootClass(string | Htmlable | Closure | null $name): static
    {
        $this->rootClass = $name;

        return $this;
    }

    public function routeRootClass(): static
    {
        $this->rootClass = function (Request $request): ?string {
            if ($name = $request->route()?->getName()) {
                return str($name)->replace('.', ' ')->snake()->slug();
            }

            return null;
        };

        return $this;
    }

    public function getRootClass(): string | Htmlable | null
    {
        return $this->evaluate($this->rootClass);
    }
}

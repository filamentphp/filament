<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Exception;
use Filament\Support\Livewire\Partials\PartialsComponentHook;

trait CanPartiallyRender
{
    /**
     * @var array<string>
     */
    protected array $componentsToPartiallyRenderAfterStateUpdated = [];

    protected bool | Closure $isRenderlessAfterStateUpdated = false;

    protected bool | Closure $isPartiallyRenderedAfterStateUpdated = false;

    /**
     * @param  array<string>  $components
     */
    public function partiallyRenderComponentsAfterStateUpdated(array $components): static
    {
        $this->componentsToPartiallyRenderAfterStateUpdated = $components;

        return $this;
    }

    public function skipRenderAfterStateUpdated(bool | Closure $condition = true): static
    {
        $this->isRenderlessAfterStateUpdated = $condition;

        return $this;
    }

    public function partiallyRenderAfterStateUpdated(bool | Closure $condition = true): static
    {
        $this->isPartiallyRenderedAfterStateUpdated = $condition;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getComponentsToPartiallyRenderAfterStateUpdated(): array
    {
        return $this->componentsToPartiallyRenderAfterStateUpdated;
    }

    public function isRenderlessAfterStateUpdated(): bool
    {
        return (bool) $this->evaluate($this->isRenderlessAfterStateUpdated);
    }

    public function isPartiallyRenderedAfterStateUpdated(): bool
    {
        return (bool) $this->evaluate($this->isPartiallyRenderedAfterStateUpdated);
    }

    public function partiallyRender(): void
    {
        app(PartialsComponentHook::class)->renderPartial($this->getLivewire(), function (): array {
            $key = $this->getKey();

            if (blank($key)) {
                throw new Exception('A [key()] or [statePath()] is required to partially render a component.');
            }

            return [
                "schema-component::{$key}" => $this->hasView() ? $this->render() : $this->toHtml(...),
            ];
        });
    }

    public function skipRender(): void
    {
        app(PartialsComponentHook::class)->skipPartialRender($this->getLivewire());
    }
}

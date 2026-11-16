<?php

namespace Filament\Support\Concerns;

use Closure;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Support\State\LazyLoadedStateGroup;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

use function Filament\Support\generate_icon_html;
use function Filament\Support\generate_loading_indicator_html;

trait CanLazyLoadState
{
    protected bool | Closure $isStateLazyLoaded = false;

    protected ?LazyLoadedStateGroup $lazyLoadedStateGroup = null;

    protected string | Closure | null $lazyLoadedStateKey = null;

    protected bool $isRenderingLazyLoadedState = false;

    public function lazyLoadedState(bool | Closure $condition = true, ?LazyLoadedStateGroup $group = null, string | Closure | null $key = null): static
    {
        $this->isStateLazyLoaded = $condition;
        $this->lazyLoadedStateGroup = $group;
        $this->lazyLoadedStateKey = $key;

        return $this;
    }

    public function isStateLazyLoaded(): bool
    {
        return (bool) $this->evaluate($this->isStateLazyLoaded);
    }

    public function getLazyLoadedStateGroup(): ?LazyLoadedStateGroup
    {
        return $this->lazyLoadedStateGroup;
    }

    public function getLazyLoadedStateKey(): string
    {
        return (string) ($this->evaluate($this->lazyLoadedStateKey) ?? $this->getName());
    }

    /**
     * @internal
     */
    public function renderLazyLoadedState(bool $hasState = false, mixed $state = null): string
    {
        $component = method_exists($this, 'getClone') ? $this->getClone() : clone $this;
        $component->isRenderingLazyLoadedState = true;
        $component->viewInstances = [];

        if ($hasState) {
            $component->state(static fn (): mixed => $state);
        }

        if (method_exists($component, 'clearCachedState')) {
            $component->clearCachedState();
        }

        return $component->toHtml();
    }

    /**
     * @param  array<string, mixed>  $target
     * @param  array<mixed>  $arguments
     */
    protected function getLazyLoadedStatePlaceholderHtml(string $method, array $target, array $arguments = []): string
    {
        $identifier = (string) Str::uuid();
        $configuration = Js::from([
            'id' => $identifier,
            'group' => $this->getLazyLoadedStateGroup()?->getName($this) ?? $identifier,
            'method' => $method,
            'arguments' => $arguments,
            'target' => $target,
        ]);

        $placeholder = '<span x-show="isLoading" role="status" class="fi-lazy-loaded-state-loading">'
            . generate_loading_indicator_html(size: IconSize::Small)->toHtml()
            . '<span>' . e(__('filament::loading.label')) . '</span></span>'
            . '<span x-cloak x-show="hasFailed" role="alert" class="fi-lazy-loaded-state-error">'
            . generate_icon_html(Heroicon::ExclamationCircle, size: IconSize::Small)->toHtml()
            . '<span>' . e(__('filament::loading.error')) . '</span></span>';

        return '<div wire:key="' . e($identifier) . '" x-data="filamentLazyLoadedState(' . e($configuration) . ')" :aria-busy="isLoading">'
            . '<div x-ref="content">' . $this->wrapLazyLoadedStatePlaceholderHtml($placeholder) . '</div></div>';
    }
}

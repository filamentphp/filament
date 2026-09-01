<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Livewire\Livewire;

class LivewireField extends Field implements HasEmbeddedView
{
    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.livewire-field';

    protected bool | Closure $isLazy = false;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $data = [];

    protected string | Closure $component;

    public function component(string | Closure $component): static
    {
        $this->component = $component;

        return $this;
    }

    public function getComponent(): string
    {
        return $this->evaluate($this->component);
    }

    public function lazy(bool | Closure $condition = true): static
    {
        $this->isLazy = $condition;

        return $this;
    }

    public function isLazy(): bool
    {
        return (bool) $this->evaluate($this->isLazy);
    }

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function data(array | Closure $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->evaluate($this->data);
    }

    /**
     * @return array<string, mixed>
     */
    public function getComponentProperties(): array
    {
        $properties = [
            'record' => $this->getRecord(),
            $this->applyStateBindingModifiers('wire:model') => $this->getStatePath(),
        ];

        if ($this->isLazy()) {
            $properties['lazy'] = true;
        }

        return [
            ...$properties,
            ...$this->getData(),
        ];
    }

    public function toEmbeddedHtml(): string
    {
        $extraAttributes = $this->getExtraAttributes();
        $id = $this->getId();
        $hasWrapper = filled($id) || filled($extraAttributes);

        $livewireHtml = Livewire::mount($this->getComponent(), $this->getComponentProperties(), $this->getLivewireKey());

        if ($hasWrapper) {
            $attributes = (new FilamentComponentAttributeBag)
                ->merge([
                    'aria-labelledby' => filled($id) ? "{$id}-label" : null,
                    'id' => $id,
                    'role' => filled($id) ? 'group' : null,
                ], escape: false)
                ->merge($extraAttributes, escape: false);

            $livewireHtml = '<div ' . $attributes->toHtml() . '>' . $livewireHtml . '</div>';
        }

        return $this->wrapEmbeddedHtml($livewireHtml, labelTag: filled($id) ? 'div' : 'label');
    }
}

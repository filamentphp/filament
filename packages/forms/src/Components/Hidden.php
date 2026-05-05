<?php

namespace Filament\Forms\Components;

use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;

class Hidden extends Field implements HasEmbeddedView
{
    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.hidden';

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan(['default' => 'hidden']);
    }

    public function toEmbeddedHtml(): string
    {
        $attributes = (new FilamentComponentAttributeBag)
            ->merge([
                'id' => $this->getId(),
                'type' => 'hidden',
                $this->applyStateBindingModifiers('wire:model') => $this->getStatePath(),
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class(['fi-fo-hidden']);

        return '<input ' . $attributes->toHtml() . ' />';
    }
}

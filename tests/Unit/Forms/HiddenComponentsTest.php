<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Tests\TestCase;
use Tests\Unit\Forms\Fixtures\Livewire;

uses(TestCase::class);

test('components can be hidden', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->hidden();

    expect($component)
        ->isHidden()->toBeTrue();
});

test('hidden components are not returned from container', function () {
    $components = [];

    foreach (range(1, $visibleCount = rand(2, 10)) as $i) {
        $components[] = new Component();
    }

    foreach (range(1, rand(2, 10)) as $i) {
        $components[] = (new Component())->hidden();
    }

    $componentsBoundToContainer = ($container = ComponentContainer::make(Livewire::make()))
        ->components($components)
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($visibleCount)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->isHidden()->toBeFalse()
                ->getContainer()->toBe($container),
        );
});

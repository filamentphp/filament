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

test('components can be hidden based on condition', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->components([
            (new Component())
                ->when(fn (callable $get) => $get('foo_bar') === false),
        ])
        ->fill([
            'foo_bar' => true,
        ]);

    expect($container->getComponents())
        ->toHaveCount(0);

    $container->components([
        (new Component())
            ->whenTruthy('foo_bar'),
    ]);

    expect($container->getComponents())
        ->toHaveLength(1);

    $container->components([
        (new Component())
            ->whenFalsy('foo_bar'),
    ]);

    expect($container->getComponents())
        ->toHaveLength(0);
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

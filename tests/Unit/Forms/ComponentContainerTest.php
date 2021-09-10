<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Unit\Forms\Fixtures\Livewire;

uses(TestCase::class);

it('belongs to Livewire component', function () {
    $container = ComponentContainer::make($livewire = Livewire::make());

    expect($container)
        ->getLivewire()->toBe($livewire);
});

it('has components', function () {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component();
    }

    $componentsBoundToContainer = ($container = ComponentContainer::make(Livewire::make()))
        ->components($components)
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->toBe($container),
        );
});

it('belongs to parent component', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->parentComponent($component = new Component());

    expect($container)
        ->getParentComponent()->toBe($component);
});

it('can return a component by name and callback', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->components([
            $input = Field::make($statePath = Str::random()),
        ]);

    expect($container)
        ->getComponent($statePath)->toBe($input)
        ->getComponent(fn (Component $component) => $component->getName() === $statePath)->toBe($input);
});

<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('components can be hidden', function () {
    $component = (new Component)
        ->container(ComponentContainer::make(Livewire::make()))
        ->hidden();

    expect($component)
        ->isHidden()->toBeTrue();
});

test('components can be hidden based on condition', function () {
    $statePath = Str::random();

    $container = ComponentContainer::make(Livewire::make())
        ->components([
            (new Component)
                ->visible(fn (callable $get) => $get($statePath) === false),
        ])
        ->fill([
            $statePath => true,
        ]);

    expect($container->getComponents())
        ->toHaveCount(0);

    $container->components([
        (new Component)
            ->whenTruthy($statePath),
    ]);

    expect($container->getComponents())
        ->toHaveLength(1);

    $container->components([
        (new Component)
            ->whenFalsy($statePath),
    ]);

    expect($container->getComponents())
        ->toHaveLength(0);

    $container
        ->components([
            (new Component)
                ->whenFalsy([$statePath, 'bob']),
        ])
        ->fill([
            'bob' => true,
        ]);

    expect($container->getComponents())
        ->toHaveLength(0);

    $container
        ->components([
            (new Component)
                ->whenTruthy([$statePath, 'bob']),
        ])
        ->fill([
            'bob' => true,
        ]);

    expect($container->getComponents())
        ->toHaveLength(1);
});

test('hidden components are not returned from container', function () {
    $components = [];

    foreach (range(1, $visibleCount = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    foreach (range(1, rand(2, 10)) as $i) {
        $components[] = (new Component)->hidden();
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

test('components can be hidden based on Livewire component', function () {
    $components = ComponentContainer::make(Foo::make())
        ->components([
            TextInput::make('foo')
                ->hiddenOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(0);

    $components = ComponentContainer::make(Bar::make())
        ->components([
            TextInput::make('foo')
                ->hiddenOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(1)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(TextInput::class)
                ->isHidden()->toBeFalse()
        );

    $components = ComponentContainer::make(Bar::make())
        ->components([
            TextInput::make('foo')
                ->hiddenOn([Foo::class, Bar::class]),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(0);
});

test('components can be visible based on Livewire component', function () {
    $components = ComponentContainer::make(Foo::make())
        ->components([
            TextInput::make('foo')
                ->visibleOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(1);

    $components = ComponentContainer::make(Bar::make())
        ->components([
            TextInput::make('foo')
                ->visibleOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(0)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(TextInput::class)
                ->isHidden()->toBeFalse()
        );

    $components = ComponentContainer::make(Bar::make())
        ->components([
            TextInput::make('foo')
                ->visibleOn([Foo::class, Bar::class]),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(1);
});

class Foo extends Livewire {}
class Bar extends Livewire {}

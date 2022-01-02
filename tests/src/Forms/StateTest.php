<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('container has state path', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath($containerStatePath = Str::random());

    expect($container)
        ->getStatePath()->toBe($containerStatePath);
});

test('container has state path and inherits state path from parent component', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->parentComponent(
            (new Component())
                ->container(ComponentContainer::make(Livewire::make()))
                ->statePath($parentComponentStatePath = Str::random()),
        )
        ->statePath($containerStatePath = Str::random());

    expect($container)
        ->getStatePath()->toBe("{$parentComponentStatePath}.{$containerStatePath}");
});

test('component has state path', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->statePath($componentStatePath = Str::random());

    expect($component)
        ->getStatePath()->toBe($componentStatePath);
});

test('component inherits state path from container', function () {
    $component = (new Component())
        ->container(
            ComponentContainer::make(Livewire::make())
                ->statePath($containerStatePath = Str::random()),
        );

    expect($component)
        ->getStatePath()->toBe($containerStatePath);
});

test('component has state path and inherits state path from container', function () {
    $component = (new Component())
        ->container(
            ComponentContainer::make(Livewire::make())
                ->statePath($containerStatePath = Str::random()),
        )
        ->statePath($componentStatePath = Str::random());

    expect($component)
        ->getStatePath()->toBe("{$containerStatePath}.{$componentStatePath}");
});

test('state can be hydrated from array', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random()),
        ])
        ->fill([$statePath => ($state = Str::random())]);

    expect($livewire)
        ->getData()->toBe([$statePath => $state]);
});

test('state can be hydrated from defaults', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
        ])
        ->fill();

    expect($livewire)
        ->getData()->toBe([$statePath => $state]);
});

test('custom logic can be executed after state is hydrated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->afterStateHydrated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill([$statePath => ($value = Str::random())]);

    expect($livewire)
        ->getData()->toBe([$statePath => strrev($value)]);
});

test('custom logic can be executed after state is updated', function () {
    ComponentContainer::make($livewire = Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->afterStateUpdated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill([$statePath => ($state = Str::random())])
        ->callAfterStateUpdated("data.{$statePath}");

    expect($livewire)
        ->getData()->toBe([$statePath => strrev($state)]);
});

test('state can be dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->default($state = Str::random()),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => $state],
        ]);
});

test('state can be dehydrated using custom logic', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->dehydrateStateUsing(fn ($state) => strrev($state)),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

test('custom logic can be executed before state is dehydrated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->beforeStateDehydrated(fn (Component $component, $state) => $component->state(strrev($state))),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

test('components can be excluded from state dehydration', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath(Str::random())
                ->default(Str::random())
                ->dehydrated(false),
        ])
        ->fill();

    expect($container)
        ->dehydrateState()->toBe([]);
});

test('dehydrated state can be mutated', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component())
                ->statePath($statePath = Str::random())
                ->default($state = Str::random())
                ->mutateDehydratedStateUsing(fn ($state) => strrev($state)),
        ])
        ->fill();

    expect($container)
        ->mutateDehydratedState($container->dehydrateState())->toBe([
            'data' => [$statePath => strrev($state)],
        ]);
});

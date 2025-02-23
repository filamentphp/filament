<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('components can be hidden', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->hidden();

    expect($component)
        ->isHidden()->toBeTrue();
});

test('components can be hidden based on condition', function (): void {
    $statePath = Str::random();

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            (new Component)
                ->visible(fn (callable $get) => $get($statePath) === false),
        ])
        ->fill([
            $statePath => true,
        ]);

    expect($schema->getComponents())
        ->toHaveCount(0);

    $schema->components([
        (new Component)
            ->whenTruthy($statePath),
    ]);

    expect($schema->getComponents())
        ->toHaveLength(1);

    $schema->components([
        (new Component)
            ->whenFalsy($statePath),
    ]);

    expect($schema->getComponents())
        ->toHaveLength(0);

    $schema
        ->components([
            (new Component)
                ->whenFalsy([$statePath, 'bob']),
        ])
        ->fill([
            $statePath => true,
            'bob' => true,
        ]);

    expect($schema->getComponents())
        ->toHaveLength(0);

    $schema
        ->components([
            (new Component)
                ->whenTruthy([$statePath, 'bob']),
        ])
        ->fill([
            $statePath => true,
            'bob' => true,
        ]);

    expect($schema->getComponents())
        ->toHaveLength(1);
});

test('hidden components are not returned from container', function (): void {
    $components = [];

    foreach (range(1, $visibleCount = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    foreach (range(1, rand(2, 10)) as $i) {
        $components[] = (new Component)->hidden();
    }

    $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
        ->components($components)
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($visibleCount)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->isHidden()->toBeFalse()
                ->getContainer()->toBe($schema),
        );
});

test('components can be hidden based on Livewire component', function (): void {
    $components = Schema::make(Foo::make())
        ->components([
            TextInput::make('foo')
                ->hiddenOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(0);

    $components = Schema::make(Bar::make())
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

    $components = Schema::make(Bar::make())
        ->components([
            TextInput::make('foo')
                ->hiddenOn([Foo::class, Bar::class]),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(0);
});

test('components can be visible based on Livewire component', function (): void {
    $components = Schema::make(Foo::make())
        ->components([
            TextInput::make('foo')
                ->visibleOn(Foo::class),
        ])
        ->getComponents();

    expect($components)
        ->toHaveLength(1);

    $components = Schema::make(Bar::make())
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

    $components = Schema::make(Bar::make())
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

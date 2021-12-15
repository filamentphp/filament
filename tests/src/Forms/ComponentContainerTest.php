<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

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

it('has dynamic components', function () {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component();
    }

    $componentsBoundToContainer = ($container = ComponentContainer::make(Livewire::make()))
        ->components(fn (): array => $components)
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

it('can return a flat array of components', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->components([
            $fieldset = Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make(Str::random()),
                ]),
            $section = Section::make(Str::random()),
        ]);

    expect($container)
        ->getFlatComponents()
        ->toHaveCount(3)
        ->toMatchArray([
            $fieldset,
            $field,
            $section,
        ]);
});

it('can return a flat array of fields', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            $section = Section::make(Str::random()),
        ]);

    expect($container)
        ->getFlatFields()
        ->toHaveCount(1)
        ->toMatchArray([
            $name => $field,
        ]);
});

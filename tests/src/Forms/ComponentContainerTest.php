<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Fieldset;
use Filament\Schema\Components\Section;
use Filament\Schema\Schema;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('belongs to Livewire component', function () {
    $container = Schema::make($livewire = Livewire::make());

    expect($container)
        ->getLivewire()->toBe($livewire);
});

it('has components', function () {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($container = Schema::make(Livewire::make()))
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
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($container = Schema::make(Livewire::make()))
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
    $container = Schema::make(Livewire::make())
        ->parentComponent($component = new Component);

    expect($container)
        ->getParentComponent()->toBe($component);
});

it('can return a component by name and callback', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            $input = Field::make($statePath = Str::random()),
        ]);

    expect($container)
        ->getComponent($statePath)->toBe($input)
        ->getComponent(fn (Component $component) => $component->getName() === $statePath)->toBe($input);
});

it('can return a flat array of components', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            $fieldset = Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($fieldName = Str::random()),
                ]),
            $section = Section::make($sectionHeading = Str::random()),
        ]);

    expect($container)
        ->getFlatComponents()
        ->toHaveCount(3)
        ->toBe([
            $fieldset,
            $fieldName => $field,
            Str::slug($sectionHeading) => $section,
        ]);
});

it('can return a flat array of components with hidden components', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            $fieldset = Fieldset::make(Str::random())
                ->hidden()
                ->schema([
                    $field = TextInput::make($fieldName = Str::random()),
                ]),
            $section = Section::make($sectionHeading = Str::random()),
        ]);

    expect($container)
        ->getFlatComponents(withHidden: true)
        ->toHaveCount(3)
        ->toBe([
            $fieldset,
            $fieldName => $field,
            Str::slug($sectionHeading) => $section,
        ]);
});

it('can return a flat array of fields', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($container)
        ->getFlatFields()
        ->toHaveCount(1)
        ->toMatchArray([
            $name => $field,
        ]);
});

it('can return a flat array of fields with hidden fields', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->hidden()
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($container)
        ->getFlatFields(withHidden: true)
        ->toHaveCount(1)
        ->toMatchArray([
            $name => $field,
        ]);
});

it('can return a flat array of fields with nested path keys', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ])
                ->statePath($fieldsetStatePath = Str::random()),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($container)
        ->getFlatFields()
        ->toHaveCount(1)
        ->toMatchArray([
            "{$fieldsetStatePath}.{$name}" => $field,
        ]);
});

it('can return a flat array of fields with absolute path keys', function () {
    $container = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath($containerStatePath = Str::random());

    expect($container)
        ->getFlatFields(withAbsoluteKeys: true)
        ->toHaveCount(1)
        ->toMatchArray([
            "{$containerStatePath}.{$name}" => $field,
        ]);
});

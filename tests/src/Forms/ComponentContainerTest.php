<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('belongs to Livewire component', function (): void {
    $schema = Schema::make($livewire = Livewire::make());

    expect($schema)
        ->getLivewire()->toBe($livewire);
});

it('has components', function (): void {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
        ->components($components)
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->toBe($schema),
        );
});

it('has dynamic components', function (): void {
    $components = [];

    foreach (range(1, $count = rand(2, 10)) as $i) {
        $components[] = new Component;
    }

    $componentsBoundToContainer = ($schema = Schema::make(Livewire::make()))
        ->components(fn (): array => $components)
        ->getComponents();

    expect($componentsBoundToContainer)
        ->toHaveCount($count)
        ->each(
            fn ($component) => $component
                ->toBeInstanceOf(Component::class)
                ->getContainer()->toBe($schema),
        );
});

it('belongs to parent component', function (): void {
    $schema = Schema::make(Livewire::make())
        ->parentComponent($component = new Component);

    expect($schema)
        ->getParentComponent()->toBe($component);
});

it('can return a component by name and callback', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            $input = Field::make($statePath = Str::random()),
        ]);

    expect($schema)
        ->getComponent($statePath)->toBe($input)
        ->getComponent(fn (Component $component) => $component->getName() === $statePath)->toBe($input);
});

it('can return a flat array of components', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            $fieldset = Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($fieldName = Str::random()),
                ]),
            $section = Section::make($sectionHeading = Str::random()),
        ]);

    expect($schema)
        ->getFlatComponents()
        ->toHaveCount(3)
        ->toBe([
            $fieldset,
            $fieldName => $field,
            Str::slug($sectionHeading) => $section,
        ]);
});

it('can return a flat array of components with hidden components', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            $fieldset = Fieldset::make(Str::random())
                ->hidden()
                ->schema([
                    $field = TextInput::make($fieldName = Str::random()),
                ]),
            $section = Section::make($sectionHeading = Str::random()),
        ]);

    expect($schema)
        ->getFlatComponents(withHidden: true)
        ->toHaveCount(3)
        ->toBe([
            $fieldset,
            $fieldName => $field,
            Str::slug($sectionHeading) => $section,
        ]);
});

it('can return a flat array of fields', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($schema)
        ->getFlatFields()
        ->toHaveCount(1)
        ->toMatchArray([
            $name => $field,
        ]);
});

it('can return a flat array of fields with hidden fields', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->hidden()
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($schema)
        ->getFlatFields(withHidden: true)
        ->toHaveCount(1)
        ->toMatchArray([
            $name => $field,
        ]);
});

it('can return a flat array of fields with nested path keys', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ])
                ->statePath($fieldsetStatePath = Str::random()),
            Section::make(Str::random()),
        ])
        ->statePath(Str::random());

    expect($schema)
        ->getFlatFields()
        ->toHaveCount(1)
        ->toMatchArray([
            "{$fieldsetStatePath}.{$name}" => $field,
        ]);
});

it('can return a flat array of fields with absolute path keys', function (): void {
    $schema = Schema::make(Livewire::make())
        ->components([
            Fieldset::make(Str::random())
                ->schema([
                    $field = TextInput::make($name = Str::random()),
                ]),
            Section::make(Str::random()),
        ])
        ->statePath($schemaStatePath = Str::random());

    expect($schema)
        ->getFlatFields(withAbsoluteKeys: true)
        ->toHaveCount(1)
        ->toMatchArray([
            "{$schemaStatePath}.{$name}" => $field,
        ]);
});

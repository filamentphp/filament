<?php

use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

test('fields can have state binding modifiers', function (): void {
    $field = (new Field(Str::random()))
        ->container(Schema::make(Livewire::make()))
        ->stateBindingModifiers($modifiers = [Str::random(), Str::random()]);

    expect($field)
        ->applyStateBindingModifiers($expression = Str::random())
        ->toBe(
            implode(
                '.',
                [
                    $expression,
                    ...$modifiers,
                ],
            ),
        );
});

test('component state binding is deferred by default', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()));

    expect($component)
        ->getStateBindingModifiers()->toBe([]);
});

test('component state binding can be live', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->live();

    expect($component)
        ->getStateBindingModifiers()->toBe(['live']);
});

test('component state binding can be triggered on blur', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->live(onBlur: true);

    expect($component)
        ->getStateBindingModifiers()->toBe(['blur']);
});

test('component state binding can be debounced', function (): void {
    $component = (new Component)
        ->container(Schema::make(Livewire::make()))
        ->live(debounce: '750ms');

    expect($component)
        ->getStateBindingModifiers()->toBe(['live', 'debounce', '750ms']);
});

test('components inherit their state binding modifiers', function (): void {
    $component = (new Component)
        ->container(
            Schema::make(Livewire::make())
                ->parentComponent(
                    (new Component)->stateBindingModifiers($modifiers = [Str::random()]),
                ),
        );

    expect($component)
        ->getStateBindingModifiers()->toBe($modifiers);
});

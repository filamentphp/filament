<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('sets its state path from its name', function () {
    $field = (new Field($name = Str::random()))
        ->container(ComponentContainer::make(Livewire::make()));

    expect($field)
        ->getStatePath()->toBe($name);
});

it('sets its fallback label from its name', function () {
    $field = (new Field($name = Str::random()))
        ->container(ComponentContainer::make(Livewire::make()));

    expect($field)
        ->getLabel()->toBe(
            (string) Str::of($name)
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );
});

it('has state binding modifiers', function () {
    $field = (new Field(Str::random()))
        ->container(ComponentContainer::make(Livewire::make()))
        ->stateBindingModifiers($modifiers = [Str::random(), Str::random()]);

    expect($field)
        ->applyStateBindingModifiers($expression = Str::random())->toBe(implode('.', array_merge([$expression], $modifiers)));
});

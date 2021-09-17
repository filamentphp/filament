<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Unit\Forms\Fixtures\Livewire;

uses(TestCase::class);

test('components can be reactive', function () {
    $component = ((new Component()))
        ->container(ComponentContainer::make(Livewire::make()))
        ->reactive();

    expect($component)
        ->getStateBindingModifiers()->toBe([]);
});

test('components can be lazy', function () {
    $component = ((new Component()))
        ->container(ComponentContainer::make(Livewire::make()))
        ->lazy();

    expect($component)
        ->getStateBindingModifiers()->toBe(['lazy']);
});

test('components with children pass through reactive calls', function () {
    $component = ((new Component()))
        ->container(ComponentContainer::make(Livewire::make()))
        ->childComponents([
            TextInput::make('foo')
        ])
        ->reactive();

    expect($component)
        ->getChildComponents()->{0}
        ->getStateBindingModifiers()->toBe([]);
});

test('components with children pass through lazy calls', function () {
    $component = ((new Component()))
        ->container(ComponentContainer::make(Livewire::make()))
        ->childComponents([
            TextInput::make('foo')
        ])
        ->lazy();

    expect($component)
        ->getChildComponents()->{0}
        ->getStateBindingModifiers()->toBe(['lazy']);
});

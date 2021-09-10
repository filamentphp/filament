<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Tests\TestCase;
use Tests\Unit\Forms\Fixtures\Livewire;

uses(TestCase::class);

test('components can be conditionally modified', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->if(true, fn (Component $component) => $component->label('Foo'));

    expect($component)
        ->getLabel()->toBe('Foo');

    $component->if(fn () => true, fn (Component $component) => $component->label('Bar'));

    expect($component)
        ->getLabel()->toBe('Bar');

    $component->if(
        fn () => false,
        fn (Component $component) => $component->label('Bob'),
        fn (Component $component) => $component->label('Baz')
    );

    expect($component)
            ->getLabel()->toBe('Baz');
});

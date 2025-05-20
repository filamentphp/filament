<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Support\Components\Component;
use Filament\Tests\TestCase;

uses(TestCase::class);

test('component is macroable', function () {
    expect(ComponentContainer::hasMacro('someMacro'))
        ->toBeFalse();

    expect(ComponentContainer::hasMacro('someMacro'))
        ->toBeFalse();

    ComponentContainer::macro('someMacro', fn () => 'Hello');

    expect(ComponentContainer::hasMacro('someMacro'))
        ->toBeTrue();

    expect(Form::hasMacro('someMacro'))
        ->toBeTrue(); // Descendant of `Component`...

    expect(Field::hasMacro('someMacro'))
        ->toBeFalse();
});

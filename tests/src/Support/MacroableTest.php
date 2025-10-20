<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Components\Component;
use Filament\Tests\TestCase;

uses(TestCase::class);

test('component is macroable', function (): void {
    expect(Field::hasMacro('someMacro'))
        ->toBeFalse();

    expect(Field::hasMacro('someMacro'))
        ->toBeFalse();

    Field::macro('someMacro', fn () => 'Hello');

    expect(Field::hasMacro('someMacro'))
        ->toBeTrue();

    expect(TextInput::hasMacro('someMacro'))
        ->toBeTrue(); // Descendant of `Component`...

    expect(Section::hasMacro('someMacro'))
        ->toBeFalse();
});

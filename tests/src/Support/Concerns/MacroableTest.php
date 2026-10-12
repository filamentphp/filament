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

it('can use `mixin()` to register macros from public and protected methods', function (): void {
    expect(Field::hasMacro('publicMixinMacro'))
        ->toBeFalse()
        ->and(Field::hasMacro('protectedMixinMacro'))
        ->toBeFalse();

    Field::mixin(new FieldMixin);

    expect(Field::hasMacro('publicMixinMacro'))
        ->toBeTrue()
        ->and(Field::hasMacro('protectedMixinMacro'))
        ->toBeTrue();

    expect(TextInput::make('name')->publicMixinMacro())
        ->toBe('Hello')
        ->and(TextInput::make('name')->protectedMixinMacro())
        ->toBe('Hi');
});

class FieldMixin
{
    public function publicMixinMacro(): Closure
    {
        return fn (): string => 'Hello';
    }

    protected function protectedMixinMacro(): Closure
    {
        return fn (): string => 'Hi';
    }
}

<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('sets its state path from its name', function (): void {
    $field = (new Field($name = Str::random()))
        ->container(Schema::make(Livewire::make()));

    expect($field)
        ->getStatePath()->toBe($name);
});

it('sets its fallback label from its name', function (): void {
    $field = (new Field($name = Str::random()))
        ->container(Schema::make(Livewire::make()));

    expect($field)
        ->getLabel()->toBe(
            (string) Str::of($name)
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );
});

it('can be instantiated with a default name', function (): void {
    $field = IdField::make();

    expect($field->getName())
        ->toBe('id');
});

it('can ignore the default name if another is specified', function (): void {
    $field = IdField::make('identifier');

    expect($field->getName())
        ->toBe('identifier');
});

class IdField extends TextInput
{
    public static function getDefaultName(): ?string
    {
        return 'id';
    }
}

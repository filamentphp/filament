<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can get the value of a field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo')
                        ->live(),
                    TextInput::make('bar')
                        ->label(fn (Get $get): string => "Label {$get('foo')}"),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'foo' => $foo = Str::random(),
        ])
        ->assertSeeText("Label {$foo}");
});

it('can get the value of a nested field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    Section::make()
                        ->statePath('nested')
                        ->schema([
                            TextInput::make('foo')
                                ->live(),
                        ]),
                    TextInput::make('bar')
                        ->label(fn (Get $get): string => "Label {$get('nested.foo')}"),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'nested.foo' => $foo = Str::random(),
        ])
        ->assertSeeText("Label {$foo}");
});

it('can get the value from a parent level field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo')
                        ->live(),
                    Section::make()
                        ->statePath('nested')
                        ->schema([
                            TextInput::make('bar')
                                ->label(fn (Get $get): string => "Label {$get('../foo')}"),
                        ]),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'foo' => $foo = Str::random(),
        ])
        ->assertSeeText("Label {$foo}");
});

it('can get the value from a parent level field with a nested field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    Section::make()
                        ->statePath('nestedOne')
                        ->schema([
                            TextInput::make('foo')
                                ->live(),
                        ]),
                    Section::make()
                        ->statePath('nestedTwo')
                        ->schema([
                            TextInput::make('bar')
                                ->label(fn (Get $get): string => "Label {$get('../nestedOne.foo')}"),
                        ]),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'nestedOne.foo' => $foo = Str::random(),
        ])
        ->assertSeeText("Label {$foo}");
});

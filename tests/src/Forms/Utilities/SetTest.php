<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set the value of a field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo'),
                    TextInput::make('bar')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, $state) => $set('foo', $state)),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'bar' => $bar = Str::random(),
        ])
        ->assertFormSet([
            'foo' => $bar,
        ]);
});

it('can set the value of a field and call its updated hook', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo')
                        ->afterStateUpdated(fn (Set $set) => $set('baz', 'qux')),
                    TextInput::make('bar')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, $state) => $set('foo', $state, shouldCallUpdatedHooks: true)),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'bar' => $bar = Str::random(),
        ])
        ->assertFormSet([
            'foo' => $bar,
            'baz' => 'qux',
        ]);
});

it('can set the value of a nested field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    Section::make()
                        ->statePath('nested')
                        ->schema([
                            TextInput::make('foo'),
                        ]),
                    TextInput::make('bar')
                        ->live()
                        ->afterStateUpdated(fn (Set $set, $state) => $set('nested.foo', $state)),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'bar' => $bar = Str::random(),
        ])
        ->assertFormSet([
            'nested.foo' => $bar,
        ]);
});

it('can set the value of a parent level field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo'),
                    Section::make()
                        ->statePath('nested')
                        ->schema([
                            TextInput::make('bar')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('../foo', $state)),
                        ]),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'nested.bar' => $bar = Str::random(),
        ])
        ->assertFormSet([
            'foo' => $bar,
        ]);
});

it('can set the value of a parent level field with a nested field', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    Section::make()
                        ->statePath('nestedOne')
                        ->schema([
                            TextInput::make('foo'),
                        ]),
                    Section::make()
                        ->statePath('nestedTwo')
                        ->schema([
                            TextInput::make('bar')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('../nestedOne.foo', $state)),
                        ]),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'nestedTwo.bar' => $bar = Str::random(),
        ])
        ->assertFormSet([
            'nestedOne.foo' => $bar,
        ]);
});

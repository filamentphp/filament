<?php

use Filament\Forms\Components\TextInput;
use Filament\Schema\Components\Section;
use Filament\Schema\Components\Utilities\Set;
use Filament\Schema\Schema;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can set the value of a field', function () {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->schema([
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

it('can set the value of a field and call its updated hook', function () {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->schema([
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

it('can set the value of a nested field', function () {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->schema([
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

it('can set the value of a parent level field', function () {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->schema([
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

it('can set the value of a parent level field with a nested field', function () {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->schema([
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

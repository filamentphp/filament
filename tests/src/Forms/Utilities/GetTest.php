<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

it('can get the updated value after set', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo')
                        ->default('initial'),
                    TextInput::make('bar')
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                            // Get value before Set
                            $before = $get('foo');

                            // Set new value
                            $set('foo', 'updated_' . $state);

                            // Get value after Set - should reflect the change
                            $after = $get('foo');

                            // Verify the caching doesn't prevent seeing the updated value
                            expect($before)->toBe('initial');
                            expect($after)->toBe('updated_' . $state);
                        }),
                ])
                ->statePath('data');
        }
    })
        ->fillForm([
            'bar' => 'test',
        ])
        ->assertSchemaStateSet([
            'foo' => 'updated_test',
        ]);
});

it('can get the same value from multiple calls', function (): void {
    livewire(new class extends Livewire
    {
        public function form(Schema $form): Schema
        {
            return $form
                ->components([
                    TextInput::make('foo')
                        ->default('test_value')
                        ->live(),
                    TextInput::make('bar')
                        ->default(function (Get $get): string {
                            // Call Get multiple times for the same field to test caching
                            $first = $get('foo');
                            $second = $get('foo');
                            $third = $get('foo');

                            // All should return the same value
                            expect($first)->toBe($second);
                            expect($second)->toBe($third);

                            return 'bar_value';
                        }),
                ])
                ->statePath('data');
        }
    })
        ->assertSchemaStateSet([
            'foo' => 'test_value',
            'bar' => 'bar_value',
        ]);
});

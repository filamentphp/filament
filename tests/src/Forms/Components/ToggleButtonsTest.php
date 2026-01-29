<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithToggleButtons::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithToggleButtons::class)
        ->fillForm(['status' => 'active'])
        ->assertSchemaStateSet(['status' => 'active']);
});

it('can render in boolean mode', function (): void {
    livewire(TestComponentWithBooleanToggleButtons::class)
        ->assertSuccessful();
});

it('can set boolean state', function (): void {
    livewire(TestComponentWithBooleanToggleButtons::class)
        ->fillForm(['is_active' => 1])
        ->assertSchemaStateSet(['is_active' => true]);
});

it('can render in multiple selection mode', function (): void {
    livewire(TestComponentWithMultipleToggleButtons::class)
        ->assertSuccessful();
});

it('can set multiple state', function (): void {
    livewire(TestComponentWithMultipleToggleButtons::class)
        ->fillForm(['tags' => ['one', 'two']])
        ->assertSchemaStateSet(['tags' => ['one', 'two']]);
});

class TestComponentWithToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithBooleanToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('is_active')->boolean(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithMultipleToggleButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('tags')
                    ->multiple()
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ]),
            ])
            ->statePath('data');
    }
}

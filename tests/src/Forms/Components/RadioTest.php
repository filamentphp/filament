<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Radio;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithRadio::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithRadio::class)
        ->fillForm(['status' => 'active'])
        ->assertSchemaStateSet(['status' => 'active']);
});

it('can render inline', function (): void {
    livewire(TestComponentWithInlineRadio::class)
        ->assertSuccessful();
});

it('can render boolean mode', function (): void {
    livewire(TestComponentWithBooleanRadio::class)
        ->assertSuccessful();
});

it('can set boolean state', function (): void {
    livewire(TestComponentWithBooleanRadio::class)
        ->fillForm(['is_active' => 1])
        ->assertSchemaStateSet(['is_active' => true]);
});

class TestComponentWithRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithInlineRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->inline(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithBooleanRadio extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Radio::make('is_active')->boolean(),
            ])
            ->statePath('data');
    }
}

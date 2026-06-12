<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithHidden::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithHidden::class)
        ->fillForm(['secret' => 'hidden-value'])
        ->assertSchemaStateSet(['secret' => 'hidden-value']);
});

it('preserves default state', function (): void {
    livewire(TestComponentWithHiddenAndDefault::class)
        ->assertSchemaStateSet(['token' => 'default-token']);
});

class TestComponentWithHidden extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Hidden::make('secret'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithHiddenAndDefault extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Hidden::make('token')->default('default-token'),
            ])
            ->statePath('data');
    }
}

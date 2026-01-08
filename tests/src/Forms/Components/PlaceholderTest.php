<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithPlaceholder::class)
        ->assertSuccessful();
});

it('can render with dynamic content', function (): void {
    livewire(TestComponentWithDynamicPlaceholder::class)
        ->assertSuccessful();
});

class TestComponentWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Placeholder::make('notice')
                    ->content('This is a placeholder'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDynamicPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Placeholder::make('info')
                    ->content(fn (): string => 'Dynamic content: ' . now()->format('Y-m-d')),
            ])
            ->statePath('data');
    }
}

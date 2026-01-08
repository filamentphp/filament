<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithViewField::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithViewField::class)
        ->fillForm(['custom' => 'test value'])
        ->assertSchemaStateSet(['custom' => 'test value']);
});

class TestComponentWithViewField extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ViewField::make('custom')
                    ->view('filament-forms::components.hidden'),
            ])
            ->statePath('data');
    }
}

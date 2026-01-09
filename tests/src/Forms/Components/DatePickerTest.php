<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithDatePicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithDatePicker::class)
        ->fillForm(['date' => '2024-01-15'])
        ->assertSchemaStateSet(['date' => '2024-01-15']);
});

it('can render with min and max date', function (): void {
    livewire(TestComponentWithDatePickerMinMax::class)
        ->assertSuccessful();
});

class TestComponentWithDatePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDatePickerMinMax extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->minDate(now()->subYear())
                    ->maxDate(now()->addYear()),
            ])
            ->statePath('data');
    }
}

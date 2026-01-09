<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithTimePicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithTimePicker::class)
        ->fillForm(['time' => '14:30:00'])
        ->assertSchemaStateSet(['time' => '14:30:00']);
});

it('can render without seconds', function (): void {
    livewire(TestComponentWithTimePickerNoSeconds::class)
        ->assertSuccessful();
});

class TestComponentWithTimePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TimePicker::make('time'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithTimePickerNoSeconds extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TimePicker::make('time')->seconds(false),
            ])
            ->statePath('data');
    }
}

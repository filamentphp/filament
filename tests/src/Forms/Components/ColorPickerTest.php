<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithColorPicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithColorPicker::class)
        ->fillForm(['color' => '#ff0000'])
        ->assertSchemaStateSet(['color' => '#ff0000']);
});

it('can render with RGB format', function (): void {
    livewire(TestComponentWithRgbColorPicker::class)
        ->assertSuccessful();
});

it('can render with RGBA format', function (): void {
    livewire(TestComponentWithRgbaColorPicker::class)
        ->assertSuccessful();
});

class TestComponentWithColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRgbColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->rgb(),
            ])
            ->statePath('data');
    }
}

class TestComponentWithRgbaColorPicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('color')->rgba(),
            ])
            ->statePath('data');
    }
}

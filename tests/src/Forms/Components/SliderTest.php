<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Slider;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithSlider::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithSlider::class)
        ->fillForm(['value' => 50])
        ->assertSchemaStateSet(['value' => 50]);
});

it('can render with custom range', function (): void {
    livewire(TestComponentWithCustomRangeSlider::class)
        ->assertSuccessful();
});

it('validates min value', function (): void {
    livewire(TestComponentWithSliderValidation::class)
        ->fillForm(['value' => -10])
        ->call('save')
        ->assertHasFormErrors(['value']);
});

it('validates max value', function (): void {
    livewire(TestComponentWithSliderValidation::class)
        ->fillForm(['value' => 200])
        ->call('save')
        ->assertHasFormErrors(['value']);
});

class TestComponentWithSlider extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithCustomRangeSlider extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(10)
                    ->maxValue(50),
            ])
            ->statePath('data');
    }
}

class TestComponentWithSliderValidation extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->minValue(0)
                    ->maxValue(100),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

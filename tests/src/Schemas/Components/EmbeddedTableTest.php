<?php

use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire as LivewireComponent;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('returns `EmbeddedTable` from `make()` when no Livewire component specified', function (): void {
    $component = EmbeddedTable::make();

    expect($component)->toBeInstanceOf(EmbeddedTable::class);
});

it('returns `Livewire` component from `make()` when Livewire component class specified', function (): void {
    $component = EmbeddedTable::make('App\\Livewire\\SomeTable');

    expect($component)->toBeInstanceOf(LivewireComponent::class);
});

it('returns `Livewire` component with data from `make()` when both specified', function (): void {
    $component = EmbeddedTable::make('App\\Livewire\\SomeTable', ['filter' => 'active']);

    expect($component)->toBeInstanceOf(LivewireComponent::class);
});

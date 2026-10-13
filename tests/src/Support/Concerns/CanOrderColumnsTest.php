<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('returns `null` column orders by default', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()));

    expect($component->getColumnOrder())->toMatchArray([
        'default' => null,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ]);
});

it('can set the column order at the default `lg` breakpoint', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnOrder(3);

    expect($component->getColumnOrder('lg'))->toBe(3);
});

it('can get the column order at one breakpoint', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnOrder(['2xl' => 5]);

    expect($component->getColumnOrder('2xl'))->toBe(5);
});

it('can get the column order at all breakpoints', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnOrder([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);

    expect($component->getColumnOrder())->toMatchArray([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);
});

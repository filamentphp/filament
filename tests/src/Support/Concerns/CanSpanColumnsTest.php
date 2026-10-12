<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can set the column span at the default `lg` breakpoint', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnSpan(3);

    expect($component->getColumnSpan('lg'))->toBe(3);
});

it('can get the column span at one breakpoint', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnSpan(['2xl' => 5]);

    expect($component->getColumnSpan('2xl'))->toBe(5);
});

it('can get the column span at all breakpoints', function (): void {
    $component = (new Component)->container(Schema::make(Livewire::make()))->columnSpan([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);

    expect($component->getColumnSpan())->toMatchArray([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);
});

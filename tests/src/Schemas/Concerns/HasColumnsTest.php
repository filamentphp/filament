<?php

use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can set the number of columns at the default `lg` breakpoint', function (): void {
    $schema = Schema::make(Livewire::make())->columns(3);

    expect($schema->getColumns('lg'))->toBe(3);
});

it('can get the number of columns at one breakpoint', function (): void {
    $schema = Schema::make(Livewire::make())->columns(['2xl' => 5]);

    expect($schema->getColumns('2xl'))->toBe(5);
});

it('can get the number of columns at all breakpoints', function (): void {
    $schema = Schema::make(Livewire::make())->columns([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);

    expect($schema->getColumns())->toMatchArray([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);
});

it('can inherit columns from its parent component', function (): void {
    $schema = Schema::make(Livewire::make())->parentComponent((new Component)->columns([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]));

    expect($schema->getColumns())->toMatchArray([
        'default' => 1,
        'sm' => 2,
        'md' => 3,
        'lg' => 4,
        'xl' => 5,
        '2xl' => 6,
    ]);
});

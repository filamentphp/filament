<?php

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Tests\Forms\Fixtures\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

test('can get number of container columns at all breakpoints', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->columns([
            'default' => $defaultColumns = rand(1, 12),
            'sm' => $columnsAtSm = rand(1, 12),
            'md' => $columnsAtMd = rand(1, 12),
            'lg' => $columnsAtLg = rand(1, 12),
            'xl' => $columnsAtXl = rand(1, 12),
            '2xl' => $columnsAt2xl = rand(1, 12),
        ]);

    expect($container)
        ->getColumns()
            ->toHaveKey('default', $defaultColumns)
            ->toHaveKey('sm', $columnsAtSm)
            ->toHaveKey('md', $columnsAtMd)
            ->toHaveKey('lg', $columnsAtLg)
            ->toHaveKey('xl', $columnsAtXl)
            ->toHaveKey('2xl', $columnsAt2xl);
});

test('can get number of container columns at one breakpoint', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->columns([
            '2xl' => $columnsAt2xl = rand(1, 12),
        ]);

    expect($container)
        ->getColumns('2xl')->toBe($columnsAt2xl);
});

test('can get number of container columns from parent component', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->parentComponent((new Component())->columns([
            'default' => $defaultColumns = rand(1, 12),
            'sm' => $columnsAtSm = rand(1, 12),
            'md' => $columnsAtMd = rand(1, 12),
            'lg' => $columnsAtLg = rand(1, 12),
            'xl' => $columnsAtXl = rand(1, 12),
            '2xl' => $columnsAt2xl = rand(1, 12),
        ]));

    expect($container)
        ->getColumns()
        ->toHaveKey('default', $defaultColumns)
        ->toHaveKey('sm', $columnsAtSm)
        ->toHaveKey('md', $columnsAtMd)
        ->toHaveKey('lg', $columnsAtLg)
        ->toHaveKey('xl', $columnsAtXl)
        ->toHaveKey('2xl', $columnsAt2xl);
});

test('can set number of container columns at `lg` breakpoint', function () {
    $container = ComponentContainer::make(Livewire::make())
        ->columns($columnsAtLg = rand(1, 12));

    expect($container)
        ->getColumns('lg')->toBe($columnsAtLg);
});

test('can get component column span at all breakpoints', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->columnSpan([
            'default' => $defaultSpan = rand(1, 12),
            'sm' => $spanAtSm = rand(1, 12),
            'md' => $spanAtMd = rand(1, 12),
            'lg' => $spanAtLg = rand(1, 12),
            'xl' => $spanAtXl = rand(1, 12),
            '2xl' => $spanAt2xl = rand(1, 12),
        ]);

    expect($component)
        ->getColumnSpan()
            ->toHaveKey('default', $defaultSpan)
            ->toHaveKey('sm', $spanAtSm)
            ->toHaveKey('md', $spanAtMd)
            ->toHaveKey('lg', $spanAtLg)
            ->toHaveKey('xl', $spanAtXl)
            ->toHaveKey('2xl', $spanAt2xl);
});

test('can get component column span at one breakpoint', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->columnSpan([
            '2xl' => $spanAt2xl = rand(1, 12),
        ]);

    expect($component)
        ->getColumnSpan('2xl')->toBe($spanAt2xl);
});

test('can set component column span at `default` breakpoint', function () {
    $component = (new Component())
        ->container(ComponentContainer::make(Livewire::make()))
        ->columnSpan($defaultSpan = rand(1, 12));

    expect($component)
        ->getColumnSpan('default')->toBe($defaultSpan);
});

<?php

use Filament\Schemas\Components\Grid;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with columns count', function (): void {
    $grid = Grid::make(3);

    expect($grid)->toBeInstanceOf(Grid::class);
});

it('defaults to `2` columns', function (): void {
    $grid = Grid::make();

    expect($grid)->toBeInstanceOf(Grid::class);
});

it('can be constructed with responsive columns array', function (): void {
    $grid = Grid::make(['default' => 1, 'sm' => 2, 'lg' => 3]);

    expect($grid)->toBeInstanceOf(Grid::class);
});

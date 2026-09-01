<?php

use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with a string label', function (): void {
    $column = TableColumn::make('Name');

    expect($column->getLabel())->toBe('Name');
});

it('can be constructed with a `Closure` label', function (): void {
    $column = TableColumn::make(static fn (): string => 'Dynamic');

    expect($column->getLabel())->toBe('Dynamic');
});

it('defaults `isHeaderLabelHidden()` to `false`', function (): void {
    $column = TableColumn::make('Name');

    expect($column->isHeaderLabelHidden())->toBeFalse();
});

it('can set `hiddenHeaderLabel()`', function (): void {
    $column = TableColumn::make('Name')->hiddenHeaderLabel();

    expect($column->isHeaderLabelHidden())->toBeTrue();
});

it('can set `hiddenHeaderLabel()` with a `Closure`', function (): void {
    $column = TableColumn::make('Name')
        ->hiddenHeaderLabel(static fn (): bool => true);

    expect($column->isHeaderLabelHidden())->toBeTrue();
});

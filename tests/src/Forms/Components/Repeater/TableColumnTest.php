<?php

use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('label', function (): void {
    it('can be constructed with a string label', function (): void {
        $column = TableColumn::make('Name');

        expect($column->getLabel())->toBe('Name');
    });

    it('can be constructed with a `Closure` label', function (): void {
        $column = TableColumn::make(static fn (): string => 'Dynamic');

        expect($column->getLabel())->toBe('Dynamic');
    });
});

describe('header label visibility', function (): void {
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
});

describe('required marker', function (): void {
    it('defaults `isMarkedAsRequired()` to `false`', function (): void {
        $column = TableColumn::make('Name');

        expect($column->isMarkedAsRequired())->toBeFalse();
    });

    it('can set `markAsRequired()`', function (): void {
        $column = TableColumn::make('Name')->markAsRequired();

        expect($column->isMarkedAsRequired())->toBeTrue();
    });

    it('can set `markAsRequired()` with a `Closure`', function (): void {
        $column = TableColumn::make('Name')
            ->markAsRequired(static fn (): bool => true);

        expect($column->isMarkedAsRequired())->toBeTrue();
    });
});

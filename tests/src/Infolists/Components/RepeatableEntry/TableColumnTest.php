<?php

use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Support\Enums\Alignment;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with a label', function (): void {
    $column = TableColumn::make('Test Label');

    expect($column->getLabel())
        ->toBe('Test Label');
});

it('can be instantiated with a closure label', function (): void {
    $column = TableColumn::make(fn () => 'Dynamic Label');

    expect($column->getLabel())
        ->toBe('Dynamic Label');
});

it('can hide header label', function (): void {
    $column = TableColumn::make('Test Label')
        ->hiddenHeaderLabel();

    expect($column->isHeaderLabelHidden())
        ->toBeTrue();
});

it('can show header label by default', function (): void {
    $column = TableColumn::make('Test Label');

    expect($column->isHeaderLabelHidden())
        ->toBeFalse();
});

it('can conditionally hide header label', function (): void {
    $column = TableColumn::make('Test Label')
        ->hiddenHeaderLabel(fn () => true);

    expect($column->isHeaderLabelHidden())
        ->toBeTrue();

    $column = TableColumn::make('Test Label')
        ->hiddenHeaderLabel(fn () => false);

    expect($column->isHeaderLabelHidden())
        ->toBeFalse();
});

it('can set alignment', function (): void {
    $column = TableColumn::make('Test Label')
        ->alignment(Alignment::Center);

    expect($column->getAlignment())
        ->toBe(Alignment::Center);
});

it('can set width', function (): void {
    $column = TableColumn::make('Test Label')
        ->width('200px');

    expect($column->getWidth())
        ->toBe('200px');
});

it('can wrap header', function (): void {
    $column = TableColumn::make('Test Label')
        ->wrapHeader();

    expect($column->canHeaderWrap())
        ->toBeTrue();
});

it('cannot wrap header by default', function (): void {
    $column = TableColumn::make('Test Label');

    expect($column->canHeaderWrap())
        ->toBeFalse();
});

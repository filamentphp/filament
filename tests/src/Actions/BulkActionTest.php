<?php

use Filament\Actions\BulkAction;
use Filament\Tests\Actions\TestCase;

uses(TestCase::class);

it('is bulk by default', function (): void {
    $action = BulkAction::make('delete');

    expect($action->isBulk())->toBeTrue();
});

it('includes `x-cloak` in `getExtraAttributes()`', function (): void {
    $action = BulkAction::make('delete');

    $attributes = $action->getExtraAttributes();

    expect($attributes)->toHaveKey('x-cloak', true);
});

it('includes `x-show` for selected records count in `getExtraAttributes()`', function (): void {
    $action = BulkAction::make('delete');

    $attributes = $action->getExtraAttributes();

    expect($attributes)->toHaveKey('x-show', 'getSelectedRecordsCount()');
});

it('merges parent extra attributes', function (): void {
    $action = BulkAction::make('delete')
        ->extraAttributes(['data-custom' => 'value']);

    $attributes = $action->getExtraAttributes();

    expect($attributes)->toHaveKey('x-cloak');
    expect($attributes)->toHaveKey('data-custom', 'value');
});

<?php

use Filament\Actions\Imports\ImportColumn;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Validator;

uses(TestCase::class);

enum ImportColumnEnumTestStatus: string
{
    case Draft = 'draft';

    case Published = 'published';
}

enum ImportColumnEnumTestPriority
{
    case Low;

    case High;
}

it('uses the cases of the enum as examples when none are set', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class);

    expect($column->getExamples())->toBe(['draft', 'published']);
});

it('uses the case names as examples for a pure enum', function (): void {
    $column = ImportColumn::make('priority')
        ->enum(ImportColumnEnumTestPriority::class);

    expect($column->getExamples())->toBe(['Low', 'High']);
});

it('keeps `examples()` when they are set alongside `enum()`', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class)
        ->examples(['draft']);

    expect($column->getExamples())->toBe(['draft']);
});

it('returns examples that can be cast to a string for the example CSV', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class);

    foreach ($column->getExamples() as $example) {
        expect($example)->not->toBeObject();
    }
});

it('validates the state against the enum', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class);

    $rules = ['status' => $column->getDataValidationRules()];

    expect(Validator::make(['status' => 'draft'], $rules)->fails())->toBeFalse();
    expect(Validator::make(['status' => 'unknown'], $rules)->fails())->toBeTrue();
});

it('does not validate against the enum when `enum()` is not used', function (): void {
    $column = ImportColumn::make('status');

    expect($column->getDataValidationRules())->toBe([]);
});

it('accepts a `Closure` for `enum()`', function (): void {
    $column = ImportColumn::make('status')
        ->enum(fn (): string => ImportColumnEnumTestStatus::class);

    expect($column->getEnum())->toBe(ImportColumnEnumTestStatus::class)
        ->and($column->getExamples())->toBe(['draft', 'published']);
});

it('returns `null` for `getEnum()` by default', function (): void {
    expect(ImportColumn::make('status')->getEnum())->toBeNull();
});

it('validates each item of a `multiple()` column against the enum', function (): void {
    $column = ImportColumn::make('statuses')
        ->multiple()
        ->enum(ImportColumnEnumTestStatus::class);

    expect($column->getDataValidationRules())->toBe([]);

    $rules = ['statuses.*' => $column->getNestedRecursiveDataValidationRules()];

    expect(Validator::make(['statuses' => ['draft', 'published']], $rules)->fails())->toBeFalse();
    expect(Validator::make(['statuses' => ['draft', 'unknown']], $rules)->fails())->toBeTrue();
});

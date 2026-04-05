<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('has `import` as default name', function (): void {
    expect(ImportAction::getDefaultName())->toBe('import');
});

it('can set `importer()`', function (): void {
    $action = ImportAction::make()
        ->importer('App\\Imports\\PostImporter');

    expect($action->getImporter())->toBe('App\\Imports\\PostImporter');
});

it('can set `job()`', function (): void {
    $action = ImportAction::make()
        ->job('App\\Jobs\\CustomImportJob');

    expect($action->getJob())->toBe('App\\Jobs\\CustomImportJob');
});

it('defaults to `ImportCsv` for `getJob()`', function (): void {
    $action = ImportAction::make();

    expect($action->getJob())->toBe(ImportCsv::class);
});

it('can set `chunkSize()`', function (): void {
    $action = ImportAction::make()
        ->chunkSize(250);

    expect($action->getChunkSize())->toBe(250);
});

it('defaults to `100` for `getChunkSize()`', function (): void {
    $action = ImportAction::make();

    expect($action->getChunkSize())->toBe(100);
});

it('can set `maxRows()`', function (): void {
    $action = ImportAction::make()
        ->maxRows(5000);

    expect($action->getMaxRows())->toBe(5000);
});

it('defaults to `null` for `getMaxRows()`', function (): void {
    $action = ImportAction::make();

    expect($action->getMaxRows())->toBeNull();
});

it('can set `headerOffset()`', function (): void {
    $action = ImportAction::make()
        ->headerOffset(2);

    expect($action->getHeaderOffset())->toBe(2);
});

it('defaults to `null` for `getHeaderOffset()`', function (): void {
    $action = ImportAction::make();

    expect($action->getHeaderOffset())->toBeNull();
});

it('can set `csvDelimiter()`', function (): void {
    $action = ImportAction::make()
        ->csvDelimiter(';');

    expect($action->getCsvDelimiter())->toBe(';');
});

it('defaults to `null` for `getCsvDelimiter()`', function (): void {
    $action = ImportAction::make();

    expect($action->getCsvDelimiter())->toBeNull();
});

it('can set `options()`', function (): void {
    $action = ImportAction::make()
        ->options(['skipHeader' => true, 'format' => 'csv']);

    expect($action->getOptions())->toBe(['skipHeader' => true, 'format' => 'csv']);
});

it('defaults to empty array for `getOptions()`', function (): void {
    $action = ImportAction::make();

    expect($action->getOptions())->toBe([]);
});

it('can add `fileRules()`', function (): void {
    $action = ImportAction::make()
        ->fileRules('max:1024');

    $rules = $action->getFileValidationRules();

    expect($rules)->toContain('max:1024');
});

it('includes `extensions:csv,txt` in default file validation rules', function (): void {
    $action = ImportAction::make();

    $rules = $action->getFileValidationRules();

    expect($rules)->toContain('extensions:csv,txt');
});

it('can set `authGuard()`', function (): void {
    $action = ImportAction::make()
        ->authGuard('admin');

    expect($action->getAuthGuard())->toBe('admin');
});

it('falls back to `ImportCsv` when `job()` is set to `null`', function (): void {
    $action = ImportAction::make()
        ->job('App\\Jobs\\CustomJob')
        ->job(null);

    expect($action->getJob())->toBe(ImportCsv::class);
});

it('can accumulate multiple `fileRules()`', function (): void {
    $action = ImportAction::make()
        ->fileRules('max:1024')
        ->fileRules('mimes:csv');

    $rules = $action->getFileValidationRules();

    expect($rules)->toContain('max:1024');
    expect($rules)->toContain('mimes:csv');
    expect($rules)->toContain('extensions:csv,txt');
});

it('can set `maxRows()` to `null` to remove limit', function (): void {
    $action = ImportAction::make()
        ->maxRows(5000)
        ->maxRows(null);

    expect($action->getMaxRows())->toBeNull();
});

it('can set `chunkSize()` with a `Closure`', function (): void {
    $action = ImportAction::make()
        ->chunkSize(static fn (): int => 500);

    expect($action->getChunkSize())->toBe(500);
});

it('can set `maxRows()` with a `Closure`', function (): void {
    $action = ImportAction::make()
        ->maxRows(static fn (): int => 10000);

    expect($action->getMaxRows())->toBe(10000);
});

it('can set `headerOffset()` with a `Closure`', function (): void {
    $action = ImportAction::make()
        ->headerOffset(static fn (): int => 3);

    expect($action->getHeaderOffset())->toBe(3);
});

it('can set `headerOffset()` to `0`', function (): void {
    $action = ImportAction::make()
        ->headerOffset(0);

    expect($action->getHeaderOffset())->toBe(0);
});

it('can set `csvDelimiter()` with a `Closure`', function (): void {
    $action = ImportAction::make()
        ->csvDelimiter(static fn (): string => "\t");

    expect($action->getCsvDelimiter())->toBe("\t");
});

it('can set `options()` with a `Closure`', function (): void {
    $action = ImportAction::make()
        ->options(static fn (): array => ['mode' => 'strict']);

    expect($action->getOptions())->toBe(['mode' => 'strict']);
});

it('can add `fileRules()` with an array', function (): void {
    $action = ImportAction::make()
        ->fileRules(['max:2048', 'mimetypes:text/csv']);

    $rules = $action->getFileValidationRules();

    expect($rules)->toContain('max:2048');
    expect($rules)->toContain('mimetypes:text/csv');
});

it('splits pipe-separated `fileRules()` string into individual rules', function (): void {
    $action = ImportAction::make()
        ->fileRules('max:1024|mimes:csv,txt');

    $rules = $action->getFileValidationRules();

    expect($rules)->toContain('max:1024');
    expect($rules)->toContain('mimes:csv,txt');
});

it('falls back to config default for `getAuthGuard()` when not set', function (): void {
    $action = ImportAction::make();

    $guard = $action->getAuthGuard();

    expect($guard)->toBeString();
    expect($guard)->not->toBeEmpty();
});

it('returns fluent `$this` from setter methods', function (): void {
    $action = ImportAction::make();

    expect($action->chunkSize(50))->toBe($action);
    expect($action->maxRows(1000))->toBe($action);
    expect($action->headerOffset(1))->toBe($action);
    expect($action->csvDelimiter(','))->toBe($action);
    expect($action->options([]))->toBe($action);
    expect($action->authGuard('web'))->toBe($action);
    expect($action->job(null))->toBe($action);
    expect($action->fileRules('max:1024'))->toBe($action);
});

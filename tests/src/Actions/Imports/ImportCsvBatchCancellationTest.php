<?php

use Carbon\CarbonImmutable;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\TestCase;

uses(TestCase::class);

/**
 * Unit test for the early-return path in `ImportCsv::handle()`.
 *
 * Scope: proves ONLY that when `$this->batch()?->cancelled()` returns true,
 * `handle()` returns before reading `$this->import->user`. It does not prove
 * batch-cancellation behaviour across real queue drivers, race conditions
 * during mid-handle() cancels, cache/replication lag, or parallel worker
 * interactions. Those rely on Laravel framework primitives that have their
 * own test coverage; this test covers only the new code added by this PR.
 *
 * Mirrors the export-side test at
 * `tests/src/Actions/Exports/ExportCsvBatchCancellationTest.php`.
 */
it('returns early from `handle()` when `batch()->cancelled()` reports true', function (): void {
    $import = Mockery::mock(Import::class)->makePartial();
    $import->shouldReceive('getImporter')
        ->with(Mockery::any(), Mockery::any())
        ->andReturn(Mockery::mock(Importer::class));

    // Proof point: if `handle()` reaches its second line it calls
    // `$this->import->user`, which Eloquent resolves via `getAttribute('user')`.
    // Asserting the mock NEVER receives that call proves the early-return
    // fired.
    $import->shouldNotReceive('getAttribute')->with('user');

    $job = new ImportCsv(
        import: $import,
        rows: [],
        columnMap: [],
    );

    [, $fakeBatch] = $job->withFakeBatch(
        id: 'test-batch-id',
        cancelledAt: CarbonImmutable::now(),
    );

    expect($fakeBatch->cancelled())->toBeTrue();

    $job->handle();
});

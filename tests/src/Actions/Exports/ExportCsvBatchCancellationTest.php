<?php

use Carbon\CarbonImmutable;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\TestCase;

uses(TestCase::class);

/**
 * Unit test for the early-return path in `ExportCsv::handle()`.
 *
 * Scope: proves ONLY that when `$this->batch()?->cancelled()` returns true,
 * `handle()` returns before reading `$this->export->user`. It does not prove
 * batch-cancellation behaviour across real queue drivers, race conditions
 * during mid-handle() cancels, cache/replication lag, or parallel worker
 * interactions. Those rely on Laravel framework primitives that have their
 * own test coverage; this test covers only the new code added by this PR.
 */
it('returns early from `handle()` when `batch()->cancelled()` reports true', function (): void {
    $export = Mockery::mock(Export::class)->makePartial();
    $export->shouldReceive('getExporter')
        ->with(Mockery::any(), Mockery::any())
        ->andReturn(Mockery::mock(Exporter::class));

    // Proof point: if `handle()` reaches its second line it calls
    // `$this->export->user`, which Eloquent resolves via `getAttribute('user')`.
    // Asserting the mock NEVER receives that call proves the early-return
    // fired.
    $export->shouldNotReceive('getAttribute')->with('user');

    $job = new ExportCsv(
        export: $export,
        query: '',
        records: [],
        page: 0,
        columnMap: [],
    );

    [, $fakeBatch] = $job->withFakeBatch(
        id: 'test-batch-id',
        cancelledAt: CarbonImmutable::now(),
    );

    expect($fakeBatch->cancelled())->toBeTrue();

    $job->handle();
});

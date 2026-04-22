<?php

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Log;

uses(TestCase::class);

/**
 * Unit test for `ImportCsv::failed()`.
 *
 * Scope: proves ONLY that `failed()` writes a structured log entry with
 * scalar-only context (`import_id`, `batch_id`, `exception_class`,
 * `exception_message`). Does not prove log channel routing, log sink
 * behaviour, or integration with Laravel's failed-jobs pipeline.
 *
 * Mirrors the export-side test at
 * `tests/src/Actions/Exports/ExportCsvFailedHandlerTest.php`.
 */
it('writes scalar-only structured log context when `failed()` fires', function (): void {
    $import = Mockery::mock(Import::class)->makePartial();
    $import->shouldReceive('getImporter')
        ->with(Mockery::any(), Mockery::any())
        ->andReturn(Mockery::mock(Importer::class));
    $import->id = 99;

    $job = new ImportCsv(
        import: $import,
        rows: [],
        columnMap: [],
    );

    Log::shouldReceive('error')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe(ImportCsv::class . ' permanently failed')
                ->and($context)->toHaveKeys(['import_id', 'batch_id', 'exception_class', 'exception_message'])
                ->and($context['import_id'])->toBe(99)
                ->and($context['batch_id'])->toBeNull()
                ->and($context['exception_class'])->toBe(RuntimeException::class)
                ->and($context['exception_message'])->toBe('simulated failure');

            return true;
        });

    $job->failed(new RuntimeException('simulated failure'));
});

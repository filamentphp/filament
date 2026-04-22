<?php

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Log;

uses(TestCase::class);

/**
 * Unit test for `ExportCsv::failed()`.
 *
 * Scope: proves ONLY that `failed()` writes a structured log entry with
 * scalar-only context (`export_id`, `page`, `batch_id`, `exception_class`,
 * `exception_message`). Does not prove log channel routing, log sink
 * behaviour, or integration with Laravel's failed-jobs pipeline.
 */
it('writes scalar-only structured log context when `failed()` fires', function (): void {
    $export = Mockery::mock(Export::class)->makePartial();
    $export->shouldReceive('getExporter')
        ->with(Mockery::any(), Mockery::any())
        ->andReturn(Mockery::mock(Exporter::class));
    $export->id = 42;

    $job = new ExportCsv(
        export: $export,
        query: '',
        records: [],
        page: 7,
        columnMap: [],
    );

    Log::shouldReceive('error')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            expect($message)->toBe(ExportCsv::class . ' permanently failed')
                ->and($context)->toHaveKeys(['export_id', 'page', 'batch_id', 'exception_class', 'exception_message'])
                ->and($context['export_id'])->toBe(42)
                ->and($context['page'])->toBe(7)
                ->and($context['batch_id'])->toBeNull()
                ->and($context['exception_class'])->toBe(RuntimeException::class)
                ->and($context['exception_message'])->toBe('simulated failure');

            return true;
        });

    $job->failed(new RuntimeException('simulated failure'));
});

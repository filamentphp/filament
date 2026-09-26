<?php

namespace Filament\Actions\Exports;

use Filament\Actions\Exports\Enums\Contracts\ExportFormat as ExportFormatInterface;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Facades\Bus;

class ExportDispatcher
{
    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     * @param  array<ExportFormatInterface>  $formats
     * @param  array<mixed> | null  $records
     * @param  class-string  $job
     */
    public function dispatch(
        Export $export,
        string $serializedQuery,
        array $columnMap,
        array $options,
        array $formats,
        ?array $records,
        string $job,
        int $chunkSize,
        ?string $jobQueue,
        ?string $jobConnection,
        ?string $jobBatchName,
        string $authGuard,
    ): void {
        $hasCsv = in_array(ExportFormat::Csv, $formats);
        $hasXlsx = in_array(ExportFormat::Xlsx, $formats);

        $makeCreateXlsxFileJob = fn (): CreateXlsxFile => app(CreateXlsxFile::class, [
            'export' => $export,
            'columnMap' => $columnMap,
            'options' => $options,
        ]);

        Bus::chain([
            Bus::batch([app($job, [
                'export' => $export,
                'query' => $serializedQuery,
                'columnMap' => $columnMap,
                'options' => $options,
                'chunkSize' => $chunkSize,
                'records' => $records,
            ])])
                ->allowFailures()
                ->when(
                    filled($jobQueue),
                    fn (PendingBatch $batch) => $batch->onQueue($jobQueue),
                )
                ->when(
                    filled($jobConnection),
                    fn (PendingBatch $batch) => $batch->onConnection($jobConnection),
                )
                ->when(
                    filled($jobBatchName),
                    fn (PendingBatch $batch) => $batch->name($jobBatchName),
                ),
            ...(($hasXlsx && (! $hasCsv)) ? [$makeCreateXlsxFileJob()] : []),
            app(ExportCompletion::class, [
                'authGuard' => $authGuard,
                'export' => $export,
                'columnMap' => $columnMap,
                'formats' => $formats,
                'options' => $options,
            ]),
            ...(($hasXlsx && $hasCsv) ? [$makeCreateXlsxFileJob()] : []),
        ])
            ->when(
                filled($jobQueue),
                fn (PendingChain $chain) => $chain->onQueue($jobQueue),
            )
            ->when(
                filled($jobConnection),
                fn (PendingChain $chain) => $chain->onConnection($jobConnection),
            )
            ->dispatch();
    }
}

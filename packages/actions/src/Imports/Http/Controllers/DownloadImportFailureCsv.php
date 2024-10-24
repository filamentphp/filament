<?php

namespace Filament\Actions\Imports\Http\Controllers;

use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Gate;
use League\Csv\ByteSequence;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function Filament\authorize;

class DownloadImportFailureCsv
{
    public function __invoke(Import $import): StreamedResponse
    {
        if (filled(Gate::getPolicyFor($import::class))) {
            authorize('view', $import);
        } else {
            abort_unless($import->user()->is(auth()->user()), 403);
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->setOutputBOM(ByteSequence::BOM_UTF8);

        $firstFailedRow = $import->failedRows()->first();

        $columnHeaders = $firstFailedRow ? array_keys($firstFailedRow->data) : [];
        $columnHeaders[] = __('filament-actions::import.failure_csv.error_header');

        $csv->insertOne($columnHeaders);

        $import->failedRows()
            ->lazyById(100)
            ->each(fn (FailedImportRow $failedImportRow) => $csv->insertOne([
                ...$failedImportRow->data,
                'error' => $failedImportRow->validation_error ?? __('filament-actions::import.failure_csv.system_error'),
            ]));

        return response()->streamDownload(function () use ($csv) {
            foreach ($csv->chunk(1000) as $offset => $chunk) {
                echo $chunk;

                if ($offset % 1000) {
                    flush();
                }
            }
        }, __('filament-actions::import.failure_csv.file_name', [
            'import_id' => $import->getKey(),
            'csv_name' => (string) str($import->file_name)->beforeLast('.')->remove('.'),
        ]) . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

<?php

namespace Filament\Actions\Imports\Http\Controllers;

use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use League\Csv\Bom;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadImportFailureCsv
{
    public function __invoke(Request $request, Import $import): StreamedResponse
    {
        abort_unless(auth(
            $request->hasValidSignature(absolute: false)
                ? $request->query('authGuard')
                : null,
        )->check(), 401);

        $user = auth(
            $request->hasValidSignature(absolute: false)
                ? $request->query('authGuard')
                : null,
        )->user();

        $importPolicy = Gate::getPolicyFor($import::class);

        if (filled($importPolicy) && method_exists($importPolicy, 'view')) {
            Gate::forUser($user)->authorize('view', Arr::wrap($import));
        } else {
            abort_unless($import->user()->is($user), 403);
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject);
        $csv->setOutputBOM(Bom::Utf8);

        if ($import->importer::shouldPreventFormulaInjection()) {
            // Security: Neutralize CSV formula injection (CWE-1236) by prefixing
            // a `'` to any cell beginning with a formula-triggering character
            // (`=`, `+`, `-`, `@`, a tab, or a carriage return). Purely numeric
            // strings such as `-5` are left unchanged, since spreadsheets treat
            // them as numbers rather than formulas, so the failure CSV can be
            // corrected and re-uploaded without corrupting legitimate data. This
            // mirrors the export column protection in `CanFormatState`.
            $csv->addFormatter(static function (array $record): array {
                return array_map(static function (mixed $cell): mixed {
                    if (! is_string($cell) || ($cell === '')) {
                        return $cell;
                    }

                    // The empty-string check above guarantees `$cell[0]` is a valid
                    // byte. A sign-led numeric string such as `-5` is left unchanged;
                    // the leading-sign guard keeps this narrow, since `is_numeric()`
                    // also accepts a leading tab or carriage return (e.g. "\t5"),
                    // which are formula triggers that must still be escaped.
                    if (in_array($cell[0], ['-', '+'], strict: true) && is_numeric($cell)) {
                        return $cell;
                    }

                    if (in_array($cell[0], ['=', '+', '-', '@', "\t", "\r"], strict: true)) {
                        return "'" . $cell;
                    }

                    return $cell;
                }, $record);
            });
        }

        /** @var ?FailedImportRow $firstFailedRow */
        $firstFailedRow = $import->failedRows()->first();

        $columnHeaders = $firstFailedRow ? array_keys($firstFailedRow->data) : [];
        $columnHeaders[] = __('filament-actions::import.failure_csv.error_header');

        $csv->insertOne($columnHeaders);

        $import->failedRows()
            ->lazyById(100)
            ->each(fn (FailedImportRow $failedImportRow) => $csv->insertOne([/** @phpstan-ignore argument.type */
                ...$failedImportRow->data,
                'error' => $failedImportRow->validation_error ?? __('filament-actions::import.failure_csv.system_error'),
            ]));

        return response()->streamDownload(function () use ($csv): void {
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

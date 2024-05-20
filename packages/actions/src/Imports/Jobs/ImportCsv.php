<?php

namespace Filament\Actions\Imports\Jobs;

use Carbon\CarbonInterface;
use Exception;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class ImportCsv implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Importer $importer;

    /**
     * @param  array<array<string, string>> | string  $rows
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array | string $rows,
        protected array $columnMap,
        protected array $options = [],
    ) {
        $this->importer = $this->import->getImporter(
            $this->columnMap,
            $this->options,
        );
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return $this->importer->getJobMiddleware();
    }

    public function handle(): void
    {
        /** @var Authenticatable $user */
        $user = $this->import->user;

        if (method_exists(auth()->guard(), 'login')) {
            auth()->login($user);
        } else {
            auth()->setUser($user);
        }

        $exceptions = [];

        $processedRows = 0;
        $successfulRows = 0;

        if (! is_array($this->rows)) {
            $rows = unserialize(base64_decode($this->rows));
        }

        foreach (($rows ?? $this->rows) as $row) {
            $row = $this->utf8Encode($row);

            try {
                DB::transaction(fn () => ($this->importer)($row));
                $successfulRows++;
            } catch (RowImportFailedException $exception) {
                $this->logFailedRow($row, $exception->getMessage());
            } catch (ValidationException $exception) {
                $this->logFailedRow($row, collect($exception->errors())->flatten()->implode(' '));
            } catch (Throwable $exception) {
                $exceptions[$exception::class] = $exception;

                $this->logFailedRow($row);
            }

            $processedRows++;
        }

        $this->import->refresh();

        $importProcessedRows = $this->import->processed_rows + $processedRows;
        $this->import->processed_rows = ($importProcessedRows < $this->import->total_rows) ?
            $importProcessedRows :
            $this->import->total_rows;

        $importSuccessfulRows = $this->import->successful_rows + $successfulRows;
        $this->import->successful_rows = ($importSuccessfulRows < $this->import->total_rows) ?
            $importSuccessfulRows :
            $this->import->total_rows;

        $this->import->save();

        $this->handleExceptions($exceptions);
    }

    public function retryUntil(): ?CarbonInterface
    {
        return $this->importer->getJobRetryUntil();
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->importer->getJobTags();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function logFailedRow(array $data, ?string $validationError = null): void
    {
        $failedRow = app(FailedImportRow::class);
        $failedRow->import()->associate($this->import);
        $failedRow->data = $data;
        $failedRow->validation_error = $validationError;
        $failedRow->save();
    }

    protected function utf8Encode(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map($this->utf8Encode(...), $value);
        }

        if (is_string($value)) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return $value;
    }

    /**
     * @param  array<Throwable>  $exceptions
     */
    protected function handleExceptions(array $exceptions): void
    {
        if (empty($exceptions)) {
            return;
        }

        if (count($exceptions) > 1) {
            throw new Exception('Multiple types of exceptions occurred: [' . implode('], [', array_keys($exceptions)) . ']');
        }

        throw Arr::first($exceptions);
    }
}

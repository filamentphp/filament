<?php

namespace Filament\Actions\Imports\Jobs;

use Carbon\CarbonInterface;
use Exception;
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
     * @param  array<array<string, string>>  $rows
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $rows,
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

        auth()->login($user);

        $exceptions = [];

        $processedRows = 0;
        $successfulRows = 0;

        foreach ($this->rows as $row) {
            try {
                DB::transaction(fn () => ($this->importer)($row));
                $successfulRows++;
            } catch (ValidationException $exception) {
                $this->logFailedRow($row, collect($exception->errors())->flatten()->implode(' '));
            } catch (Throwable $exception) {
                $exceptions[$exception::class] = $exception;

                $this->logFailedRow($row);
            }

            $processedRows++;
        }

        $this->import->increment('processed_rows', $processedRows);
        $this->import->increment('successful_rows', $successfulRows);

        $this->handleExceptions($exceptions);
    }

    public function retryUntil(): CarbonInterface
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

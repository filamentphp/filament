<?php

namespace Filament\Actions\Imports\Jobs;

use Carbon\CarbonInterface;
use Filament\Actions\Imports\Events\ImportChunkProcessed;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

    /** @var array<array<string, mixed>> */
    protected array $failedRows = [];

    public ?int $maxExceptions = 5;

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

        auth()->setUser($user);

        $processedRows = 0;
        $successfulRows = 0;

        $rows = is_array($this->rows) ? $this->rows : unserialize(base64_decode($this->rows));

        DB::transaction(function () use (&$processedRows, $rows, &$successfulRows): void {
            foreach ($rows as $row) {
                $row = $this->utf8Encode($row);

                try {
                    ($this->importer)($row);
                    $successfulRows++;
                } catch (RowImportFailedException $exception) {
                    $this->logFailedRow($row, $exception->getMessage());
                } catch (ValidationException $exception) {
                    $this->logFailedRow($row, collect($exception->errors())->flatten()->implode(' '));
                } catch (Throwable $exception) {
                    report($exception);

                    $this->logFailedRow($row);
                }

                $processedRows++;
            }

            $this->import::query()
                ->whereKey($this->import)
                ->lockForUpdate()
                ->update([
                    'processed_rows' => new Expression('processed_rows + ' . $processedRows),
                    'successful_rows' => new Expression('successful_rows + ' . $successfulRows),
                ]);

            $this->import::query()
                ->whereKey($this->import)
                ->whereColumn('processed_rows', '>', 'total_rows')
                ->lockForUpdate()
                ->update([
                    'processed_rows' => new Expression('total_rows'),
                ]);

            $this->import::query()
                ->whereKey($this->import)
                ->whereColumn('successful_rows', '>', 'total_rows')
                ->lockForUpdate()
                ->update([
                    'successful_rows' => new Expression('total_rows'),
                ]);

            $this->import->failedRows()->createMany($this->failedRows);
        });

        $this->import->refresh();

        event(new ImportChunkProcessed(
            $this->import,
            $this->columnMap,
            $this->options,
            $processedRows,
            $successfulRows,
        ));
    }

    public function retryUntil(): ?CarbonInterface
    {
        return $this->importer->getJobRetryUntil();
    }

    /**
     * @return int | array<int> | null
     */
    public function backoff(): int | array | null
    {
        return $this->importer->getJobBackoff();
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
        $this->failedRows[] = [
            'data' => $this->filterSensitiveData($data),
            'validation_error' => $validationError,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function filterSensitiveData(array $data): array
    {
        return array_reduce(
            $this->importer->getColumns(),
            function (array $carry, ImportColumn $column): array {
                if (! $column->isSensitive()) {
                    return $carry;
                }

                $csvHeader = $this->columnMap[$column->getName()] ?? null;

                if (blank($csvHeader)) {
                    return $carry;
                }

                if (! array_key_exists($csvHeader, $carry)) {
                    return $carry;
                }

                unset($carry[$csvHeader]);

                return $carry;
            },
            initial: $data,
        );
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
}

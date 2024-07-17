<?php

namespace Filament\Actions\Imports\Events;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Events\Dispatchable;
use Throwable;

class ImportChunkProcessed
{
    use Dispatchable;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     * @param  array<Throwable>  $exceptions
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
        protected int $processedRows,
        protected int $successfulRows,
        protected array $exceptions,
    ) {}

    public function getImport(): Import
    {
        return $this->import;
    }

    /**
     * @return array<string, string>
     */
    public function getColumnMap(): array
    {
        return $this->columnMap;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function getSuccessfulRows(): int
    {
        return $this->successfulRows;
    }

    /**
     * @return array<Throwable>
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}

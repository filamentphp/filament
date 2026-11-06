<?php

namespace Filament\Actions\Imports\Events;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Events\Dispatchable;

class ImportChunkProcessed
{
    use Dispatchable;

    protected int $processedRowsCount;

    protected int $successfulRowsCount;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
        int $processedRows,
        int $successfulRows,
    ) {
        $this->processedRowsCount = $processedRows;
        $this->successfulRowsCount = $successfulRows;
    }

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

    /**
     * @deprecated Use `getProcessedRowsCount()` instead.
     */
    public function getProcessedRows(): int
    {
        return $this->getProcessedRowsCount();
    }

    public function getProcessedRowsCount(): int
    {
        return $this->processedRowsCount;
    }

    /**
     * @deprecated Use `getSuccessfulRowsCount()` instead.
     */
    public function getSuccessfulRows(): int
    {
        return $this->getSuccessfulRowsCount();
    }

    public function getSuccessfulRowsCount(): int
    {
        return $this->successfulRowsCount;
    }
}

<?php

namespace Filament\Actions\Imports\Events;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Events\Dispatchable;

class ImportCompleted
{
    use Dispatchable;

    /**
     * @param array<string, mixed>  $options
     * @param array<string, string> $columnMap
     */
    public function __construct(
        protected Import $import,
        protected array $options,
        protected array $columnMap,
    ) {}

    public function getImport(): Import
    {
        return $this->import;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array<string, string>
     */
    public function getColumnMap(): array
    {
        return $this->columnMap;
    }
}

<?php

namespace Filament\Actions\Imports\Events;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Events\Dispatchable;

class ImportCompleted
{
    use Dispatchable;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
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
}

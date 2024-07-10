<?php

namespace Filament\Actions\Imports\Events;

use Filament\Actions\Imports\Models\Import;
use Illuminate\Foundation\Events\Dispatchable;

class ImportCompleted
{
    use Dispatchable;

    /**
     * @param Import $import
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $options,
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
}

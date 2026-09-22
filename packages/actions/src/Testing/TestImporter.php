<?php

namespace Filament\Actions\Testing;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;

class TestImporter
{
    final public function __construct(
        protected Importer $importer,
    ) {}

    /**
     * @param  class-string<Importer>  $importer
     * @param  array<string, string> | null  $columnMap
     * @param  array<string, mixed>  $options
     */
    public static function make(string $importer, ?array $columnMap = null, array $options = [], ?Import $import = null): static
    {
        $import ??= app(Import::class);
        $import->importer = $importer;

        if ($columnMap === null) {
            $columnMap = [];

            foreach ($importer::getColumns() as $column) {
                $columnMap[$column->getName()] = $column->getName();
            }
        }

        return app(static::class, [
            'importer' => $import->getImporter($columnMap, $options),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function import(array $data): ?Model
    {
        ($this->importer)($data);

        return $this->importer->getRecord();
    }
}

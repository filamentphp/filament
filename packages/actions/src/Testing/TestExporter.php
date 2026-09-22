<?php

namespace Filament\Actions\Testing;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Model;

class TestExporter
{
    final public function __construct(
        protected Exporter $exporter,
    ) {}

    /**
     * @param  class-string<Exporter>  $exporter
     * @param  array<string, string> | null  $columnMap
     * @param  array<string, mixed>  $options
     */
    public static function make(string $exporter, ?array $columnMap = null, array $options = [], ?Export $export = null): static
    {
        $export ??= app(Export::class);
        $export->exporter = $exporter;

        $columnMap ??= collect($exporter::getVisibleColumns())
            ->filter(static fn (ExportColumn $column): bool => $column->isEnabledByDefault())
            ->mapWithKeys(static fn (ExportColumn $column): array => [$column->getName() => $column->getLabel()])
            ->all();

        return app(static::class, [
            'exporter' => $export->getExporter($columnMap, $options),
        ]);
    }

    /**
     * @return array<mixed>
     */
    public function export(Model $record): array
    {
        return ($this->exporter)($record);
    }
}

<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;

class WorkerExportActions extends ExportActions
{
    public function exportAction(): ExportAction
    {
        return parent::exportAction()
            ->exporter(WorkerPostExporter::class)
            ->chunkSize(2)
            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->orderByDesc('id'));
    }
}

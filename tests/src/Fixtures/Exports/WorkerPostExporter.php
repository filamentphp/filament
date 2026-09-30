<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Actions\Exports\ExportColumn;
use RuntimeException;

class WorkerPostExporter extends ActionPostExporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')->formatStateUsing(static function (string $state): string {
                if ($state === 'Broken') {
                    throw new RuntimeException('Deliberate worker row failure');
                }

                return $state;
            }),
            ExportColumn::make('content'),
        ];
    }
}

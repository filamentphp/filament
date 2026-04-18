<?php

namespace App\Filament\Exports;

use App\Models\Post;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PostExporter extends Exporter
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title'),
            ExportColumn::make('slug'),
            ExportColumn::make('description'),
            ExportColumn::make('status'),
            ExportColumn::make('is_featured')
                ->label('Featured'),
            ExportColumn::make('rating'),
            ExportColumn::make('author.name')
                ->label('Author'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your post export has completed. ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';
    }
}

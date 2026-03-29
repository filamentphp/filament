<?php

namespace App\Filament\Imports;

use App\Models\Post;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description')
                ->rules(['max:1024']),
            ImportColumn::make('status')
                ->rules(['in:draft,reviewing,published']),
            ImportColumn::make('is_featured')
                ->label('Featured')
                ->boolean(),
            ImportColumn::make('rating')
                ->numeric()
                ->rules(['integer', 'min:1', 'max:10']),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return new Post;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Your post import has completed. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';
    }
}

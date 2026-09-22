<?php

namespace Filament\Tests\Fixtures\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class ActionPostExporter extends Exporter
{
    protected static ?string $model = Post::class;

    public static array $getterCalls = [];

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title'),
            ExportColumn::make('rating')->enabledByDefault(false),
            ExportColumn::make('content')->hidden(),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('minimum')->numeric()->required()->default(2)];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->where('rating', '<', 9);
    }

    public function getJobQueue(): ?string
    {
        static::$getterCalls[] = 'queue';

        return 'exports';
    }

    public function getJobConnection(): ?string
    {
        static::$getterCalls[] = 'connection';

        return config('queue.default');
    }

    public function getJobBatchName(): ?string
    {
        static::$getterCalls[] = 'batch';

        return 'Post export';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return "Exported {$export->successful_rows} posts";
    }
}

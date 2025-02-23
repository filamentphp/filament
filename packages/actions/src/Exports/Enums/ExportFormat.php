<?php

namespace Filament\Actions\Exports\Enums;

use Filament\Actions\Action;
use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Filament\Actions\Exports\Downloaders\XlsxDownloader;
use Filament\Actions\Exports\Enums\Contracts\ExportFormat as ExportFormatInterface;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\URL;

enum ExportFormat: string implements ExportFormatInterface
{
    case Csv = 'csv';

    case Xlsx = 'xlsx';

    public function getDownloader(): Downloader
    {
        return match ($this) {
            self::Csv => app(CsvDownloader::class),
            self::Xlsx => app(XlsxDownloader::class),
        };
    }

    public function getDownloadNotificationAction(Export $export, string $authGuard): Action
    {
        return Action::make("download_{$this->value}")
            ->label(__("filament-actions::export.notifications.completed.actions.download_{$this->value}.label"))
            ->url(URL::signedRoute('filament.exports.download', ['authGuard' => $authGuard, 'export' => $export, 'format' => $this], absolute: false), shouldOpenInNewTab: true)
            ->markAsRead();
    }
}

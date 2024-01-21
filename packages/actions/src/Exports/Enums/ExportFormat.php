<?php

namespace Filament\Actions\Exports\Enums;

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Filament\Actions\Exports\Downloaders\XlsxDownloader;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Actions\Action as NotificationAction;

enum ExportFormat: string
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

    public function getDownloadNotificationAction(Export $export): NotificationAction
    {
        return NotificationAction::make("download_{$this->value}")
            ->label(__("filament-actions::export.notifications.completed.actions.download_{$this->value}.label"))
            ->url(route('filament.exports.download', ['export' => $export, 'format' => $this], absolute: false), shouldOpenInNewTab: true)
            ->markAsRead();
    }
}

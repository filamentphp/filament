<?php

namespace Filament\Actions\Exports\Enums;

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Filament\Actions\Exports\Downloaders\XlsxDownloader;

enum DownloadFileFormat: string
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
}

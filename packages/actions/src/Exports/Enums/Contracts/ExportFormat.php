<?php

namespace Filament\Actions\Exports\Enums\Contracts;

use Filament\Actions\Action;
use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Models\Export;

interface ExportFormat
{
    public function getDownloader(): Downloader;

    public function getDownloadNotificationAction(Export $export, string $authGuard): Action;
}

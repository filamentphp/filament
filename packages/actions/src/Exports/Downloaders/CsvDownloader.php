<?php

namespace Filament\Actions\Exports\Downloaders;

use Filament\Actions\Exports\ContentGenerators\CsvExportContentGenerator;
use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Models\Export;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvDownloader implements Downloader
{
    public function __invoke(Export $export): StreamedResponse
    {
        $disk = $export->getFileDisk();
        $directory = $export->getFileDirectory();

        if (! $disk->exists($directory)) {
            abort(404);
        }

        return response()->streamDownload(function () use ($export): void {
            foreach (app(CsvExportContentGenerator::class)($export) as $chunk) {
                echo $chunk;

                flush();
            }
        }, "{$export->file_name}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }
}

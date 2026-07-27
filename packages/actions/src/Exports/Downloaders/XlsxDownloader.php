<?php

namespace Filament\Actions\Exports\Downloaders;

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XlsxDownloader implements Downloader
{
    public function __invoke(Export $export): StreamedResponse
    {
        $disk = $export->getFileDisk();
        $directory = $export->getFileDirectory();

        if (! $disk->exists($directory)) {
            abort(404);
        }

        $fileName = $export->file_name . '.xlsx';

        if ($disk->exists($filePath = $directory . DIRECTORY_SEPARATOR . $fileName)) {
            $response = $disk->download($filePath);

            if (ob_get_length() > 0) {
                ob_end_clean();
            }

            $response->headers->set('X-Vapor-Base64-Encode', 'True');

            return $response;
        }

        $writer = app(Writer::class);

        return response()->streamDownload(function () use ($export, $fileName, $writer): void {
            $writer->openToBrowser($fileName);

            app(XlsxExportContent::class)($export, $writer);

            $writer->close();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}

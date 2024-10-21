<?php

namespace Filament\Actions\Exports\Downloaders;

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

        $fileName = "{$export->file_name}.csv";
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        if ($disk->exists($filePath)) {
            return $disk->download($filePath);
        }

        if (!config('filament.supports_stream_downloads', true)) {
            return $this->handleNonStreamedDownload($disk, $directory, $fileName, $filePath);
        }

        return $this->handleStreamedDownload($disk, $directory, $fileName);
    }

    private function handleNonStreamedDownload($disk, $directory, $fileName, $filePath): StreamedResponse
    {
        $tempPath = $this->getTempFilePath($fileName);

        $this->writeCsvContent($disk, $directory, function($content) use ($tempPath) {
            file_put_contents($tempPath, $content, FILE_APPEND);
        });

        $disk->put($filePath, file_get_contents($tempPath));
        unlink($tempPath);

        return $disk->download($filePath);
    }

    private function handleStreamedDownload($disk, $directory, $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($disk, $directory) {
            $this->writeCsvContent($disk, $directory, function($content) {
                echo $content;
                flush();
            });
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function writeCsvContent($disk, $directory, callable $outputCallback): void
    {
        $outputCallback($disk->get($directory . DIRECTORY_SEPARATOR . 'headers.csv'));

        foreach ($disk->files($directory) as $file) {
            if ($this->shouldProcessFile($file)) {
                $outputCallback($disk->get($file));
            }
        }
    }

    private function shouldProcessFile($file): bool
    {
        return !str($file)->endsWith('headers.csv') && str($file)->endsWith('.csv');
    }

    private function getTempFilePath($fileName): string
    {
        return config('filament.temp_directory', storage_path('/tmp')) . DIRECTORY_SEPARATOR . $fileName;
    }
}

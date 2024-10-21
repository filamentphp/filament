<?php

namespace Filament\Actions\Exports\Downloaders;

use Filament\Actions\Exports\Downloaders\Contracts\Downloader;
use Filament\Actions\Exports\Models\Export;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use OpenSpout\Common\Entity\Row;
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
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        if ($disk->exists($filePath)) {
            return $disk->download($filePath);
        }

        $writer = app(Writer::class);
        $csvDelimiter = $export->exporter::getCsvDelimiter();

        if (!config('filament.supports_stream_downloads', true)) {
            return $this->handleNonStreamedDownload($disk, $directory, $fileName, $filePath, $writer, $csvDelimiter);
        }

        return $this->handleStreamedDownload($disk, $directory, $fileName, $writer, $csvDelimiter);
    }

    private function handleNonStreamedDownload($disk, $directory, $fileName, $filePath, $writer, $csvDelimiter): StreamedResponse
    {
        $tempPath = $this->getTempFilePath($fileName);
        $writer->openToFile($tempPath);

        $this->writeRowsToWriter($writer, $disk, $directory, $csvDelimiter);

        $writer->close();

        $disk->put($filePath, file_get_contents($tempPath));
        unlink($tempPath);

        return $disk->download($filePath);
    }

    private function handleStreamedDownload($disk, $directory, $fileName, $writer, $csvDelimiter): StreamedResponse
    {
        return response()->streamDownload(function () use ($disk, $directory, $fileName, $writer, $csvDelimiter) {
            $writer->openToBrowser($fileName);
            $this->writeRowsToWriter($writer, $disk, $directory, $csvDelimiter);
            $writer->close();
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    private function writeRowsToWriter($writer, $disk, $directory, $csvDelimiter): void
    {
        $this->writeRowsFromFile($writer, $disk, $directory . DIRECTORY_SEPARATOR . 'headers.csv', $csvDelimiter);

        foreach ($disk->files($directory) as $file) {
            if (!$this->shouldProcessFile($file)) {
                continue;
            }
            $this->writeRowsFromFile($writer, $disk, $file, $csvDelimiter);
        }
    }

    private function writeRowsFromFile($writer, $disk, $file, $csvDelimiter): void
    {
        $csvReader = CsvReader::createFromStream($disk->readStream($file));
        $csvReader->setDelimiter($csvDelimiter);
        $csvResults = Statement::create()->process($csvReader);

        foreach ($csvResults->getRecords() as $row) {
            $writer->addRow(Row::fromValues($row));
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

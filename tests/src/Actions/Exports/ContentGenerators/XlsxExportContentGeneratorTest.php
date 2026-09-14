<?php

use Filament\Actions\Exports\ContentGenerators\XlsxExportContentGenerator;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

uses(TestCase::class);

class TestXlsxExportContentGeneratorExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export completed';
    }

    public static function getCsvDelimiter(): string
    {
        return ';';
    }
}

it('writes CSV header and data rows', function (): void {
    Storage::fake('local');

    $export = app(Export::class);
    $export->id = 1;
    $export->file_disk = 'local';
    $export->exporter = TestXlsxExportContentGeneratorExporter::class;

    $directory = $export->getFileDirectory();
    $disk = Storage::disk('local');

    $disk->put($directory . DIRECTORY_SEPARATOR . 'headers.csv', "id;name\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . '0000000000000001.csv', "1;Alpha\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . '0000000000000002.csv', "2;Beta\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . 'ignored.txt', 'ignored');

    $temporaryFile = tempnam(sys_get_temp_dir(), 'filament-xlsx-export-content');

    if ($temporaryFile === false) {
        throw new RuntimeException('Unable to create a temporary XLSX file.');
    }

    $reader = null;

    try {
        $writer = app(XlsxWriter::class);
        $writer->openToFile($temporaryFile);
        app(XlsxExportContentGenerator::class)($export, $writer);
        $writer->close();

        $reader = app(XlsxReader::class);
        $reader->open($temporaryFile);

        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }

        $reader->close();
        $reader = null;
    } finally {
        $reader?->close();

        unlink($temporaryFile);
    }

    expect($rows)->toBe([
        ['id', 'name'],
        ['1', 'Alpha'],
        ['2', 'Beta'],
    ]);
});

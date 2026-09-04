<?php

use Filament\Actions\Imports\Downloaders\Contracts\Downloader;
use Filament\Actions\Imports\Downloaders\CsvDownloader;
use Filament\Actions\Imports\Downloaders\CsvImportFailureContent;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\Csv\Bom;
use League\Csv\Reader;
use League\Csv\Writer;

uses(TestCase::class, RefreshDatabase::class);

class TestCsvImportFailureContentImporter extends Importer
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }
}

it('implements `Downloader` interface', function (): void {
    $downloader = new CsvDownloader;

    expect($downloader)->toBeInstanceOf(Downloader::class);
});

it('is invocable', function (): void {
    $downloader = new CsvDownloader;

    expect(is_callable($downloader))->toBeTrue();
});

it('writes failed import rows using `CsvImportFailureContent`', function (): void {
    $user = User::factory()->create();

    $import = Import::create([
        'file_name' => 'products.csv',
        'file_path' => 'products.csv',
        'importer' => TestCsvImportFailureContentImporter::class,
        'total_rows' => 2,
        'user_id' => $user->getKey(),
    ]);

    $import->failedRows()->createMany([
        [
            'data' => ['name' => 'Alpha'],
            'validation_error' => 'Invalid name',
        ],
        [
            'data' => ['name' => 'Beta'],
            'validation_error' => null,
        ],
    ]);

    $csv = Writer::createFromFileObject(new SplTempFileObject);

    app(CsvImportFailureContent::class)($import, $csv);

    expect($csv->getOutputBOM())->toBe(Bom::Utf8->value);

    $reader = Reader::createFromString($csv->toString());
    $reader->setHeaderOffset(0);

    expect(array_values(iterator_to_array($reader->getRecords())))->toBe([
        [
            'name' => 'Alpha',
            'error' => 'Invalid name',
        ],
        [
            'name' => 'Beta',
            'error' => 'System error, please contact support.',
        ],
    ]);
});

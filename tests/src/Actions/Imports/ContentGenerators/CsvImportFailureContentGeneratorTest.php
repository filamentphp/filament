<?php

use Filament\Actions\Imports\ContentGenerators\CsvImportFailureContentGenerator;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use League\Csv\Bom;
use League\Csv\Reader;
use League\Csv\Writer;

uses(TestCase::class, RefreshDatabase::class);

class TestCsvImportFailureContentGeneratorImporter extends Importer
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

it('writes failed import rows', function (): void {
    $user = User::factory()->create();

    $import = Import::create([
        'file_name' => 'products.csv',
        'file_path' => 'products.csv',
        'importer' => TestCsvImportFailureContentGeneratorImporter::class,
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

    app(CsvImportFailureContentGenerator::class)($import, $csv);

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

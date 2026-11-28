<?php

use Filament\Actions\Imports\ContentGenerators\CsvImportFailureContentGenerator;
use Filament\Actions\Imports\Downloaders\CsvDownloader;
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

it('preserves failed CSV cells with default and custom downloaders', function (bool $customWriter, bool $preventFormulaInjection): void {
    $import = Import::create([
        'file_name' => 'products.csv',
        'file_path' => 'products.csv',
        'importer' => TestCsvImportFailureContentGeneratorImporter::class,
        'total_rows' => 2,
        'user_id' => User::factory()->create()->getKey(),
    ]);
    $data = [
        'name' => 'quote\\",;CELL_MARKER',
        'path\\";HEADER_MARKER' => 'trailing,;slash\\',
        'description' => "line one\r\nline two",
        'ordinary' => 'NEIGHBOR_MARKER',
        'protected' => "\tINERT_MARKER",
    ];
    $error = 'error\\",;ERROR_MARKER';
    $import->failedRows()->create(['data' => $data, 'validation_error' => $error]);
    $import->failedRows()->create(['data' => array_fill_keys(array_keys($data), 'LAST_RECORD'), 'validation_error' => null]);

    TestCsvImportFailureContentGeneratorImporter::preventFormulaInjection($preventFormulaInjection);

    try {
        if ($customWriter) {
            $csv = Writer::from(new SplTempFileObject);
            $csv->setDelimiter(';');
            app(CsvImportFailureContentGenerator::class)($import, $csv);
            $content = $csv->toString();
        } else {
            $response = app(CsvDownloader::class)($import);
            ob_start();
            $response->sendContent();
            $content = ob_get_clean();
        }
    } finally {
        TestCsvImportFailureContentGeneratorImporter::preventFormulaInjection(false);
    }

    $reader = Reader::fromString($content);
    $reader->setEscape('');
    $reader->setDelimiter($customWriter ? ';' : ',');
    $headers = $reader->fetchOne();

    if ($preventFormulaInjection) {
        $data['protected'] = "'\tINERT_MARKER";
    }

    expect(str_starts_with($content, Bom::Utf8->value))->toBeTrue()
        ->and($headers)->toEqualCanonicalizing([...array_keys($data), 'error'])
        ->and(array_values(iterator_to_array($reader->getRecords())))->toBe([
            $headers,
            array_map(static fn (string $header): string => ($header === 'error') ? $error : $data[$header], $headers),
            [...array_fill(0, count($data), 'LAST_RECORD'), 'System error, please contact support.'],
        ]);
})->with([false, true])->with([false, true]);

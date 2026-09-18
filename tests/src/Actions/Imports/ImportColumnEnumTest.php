<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(TestCase::class);

enum ImportColumnEnumTestStatus: string
{
    case Draft = 'draft';

    case Published = 'published';
}

enum ImportColumnEnumTestPriority
{
    case Low;

    case High;
}

class ImportColumnEnumTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('status')
                ->enum(ImportColumnEnumTestStatus::class),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

class ImportColumnDynamicEnumTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('status')
                ->enum(static fn (Importer $importer): string => $importer->getOptions()['enum']),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

it('uses the cases of the enum as examples when none are set', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class);

    expect($column->getExamples())->toBe(['draft', 'published']);
});

it('uses the case names as examples for a pure enum', function (): void {
    $column = ImportColumn::make('priority')
        ->enum(ImportColumnEnumTestPriority::class);

    expect($column->getExamples())->toBe(['Low', 'High']);
});

it('keeps `examples()` when they are set alongside `enum()`', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class)
        ->examples(['draft']);

    expect($column->getExamples())->toBe(['draft']);
});

it('allows `examples([])` to prevent enum example rows from being generated', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class)
        ->examples([]);

    expect($column->getExamples())->toBe([]);
});

it('writes enum cases to the example CSV', function (): void {
    $response = ImportAction::make()
        ->importer(ImportColumnEnumTestImporter::class)
        ->livewire(new class extends Component {})
        ->getModalAction('downloadExample')
        ->call();

    expect($response)->toBeInstanceOf(StreamedResponse::class);

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    $reader = Reader::createFromString($content);
    $reader->setHeaderOffset(0);

    expect(iterator_to_array($reader->getRecords()))->toBe([
        1 => ['status' => 'draft'],
        2 => ['status' => 'published'],
    ]);
});

it('does not evaluate a dynamic `enum()` when writing the example CSV', function (): void {
    $response = ImportAction::make()
        ->importer(ImportColumnDynamicEnumTestImporter::class)
        ->livewire(new class extends Component {})
        ->getModalAction('downloadExample')
        ->call();

    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    $reader = Reader::createFromString($content);
    $reader->setHeaderOffset(0);

    expect($reader->getHeader())->toBe(['status'])
        ->and(iterator_to_array($reader->getRecords()))->toBe([]);
});

it('validates the state against the enum', function (): void {
    $column = ImportColumn::make('status')
        ->enum(ImportColumnEnumTestStatus::class);

    $rules = ['status' => $column->getDataValidationRules()];

    expect(Validator::make(['status' => 'draft'], $rules)->fails())->toBeFalse();
    expect(Validator::make(['status' => 'unknown'], $rules)->fails())->toBeTrue();
});

it('validates the state against a pure enum', function (): void {
    $column = ImportColumn::make('priority')
        ->enum(ImportColumnEnumTestPriority::class);

    $rules = ['priority' => $column->getDataValidationRules()];

    expect(Validator::make(['priority' => 'Low'], $rules)->fails())->toBeFalse();
    expect(Validator::make(['priority' => 'Unknown'], $rules)->fails())->toBeTrue();
});

it('does not validate against the enum when `enum()` is not used', function (): void {
    $column = ImportColumn::make('status');

    expect($column->getDataValidationRules())->toBe([]);
});

it('accepts a `Closure` for `enum()`', function (): void {
    $column = ImportColumn::make('status')
        ->enum(static fn (): string => ImportColumnEnumTestStatus::class);

    expect($column->getEnum())->toBe(ImportColumnEnumTestStatus::class)
        ->and($column->getExamples())->toBe([]);
});

it('evaluates a dynamic `enum()` with the importer while validating without evaluating it for example data', function (): void {
    $importer = new ImportColumnDynamicEnumTestImporter(
        new Import,
        ['status' => 'status'],
        ['enum' => ImportColumnEnumTestStatus::class],
    );

    expect($importer->getCachedColumns()[0]->getExamples())->toBe([]);

    $rules = $importer->getValidationRules();

    expect(Validator::make(['status' => 'draft'], $rules)->fails())->toBeFalse();
    expect(Validator::make(['status' => 'unknown'], $rules)->fails())->toBeTrue();
});

it('returns `null` for `getEnum()` by default', function (): void {
    expect(ImportColumn::make('status')->getEnum())->toBeNull();
});

it('validates each item of a `multiple()` column against the enum', function (): void {
    $column = ImportColumn::make('statuses')
        ->multiple()
        ->enum(ImportColumnEnumTestStatus::class);

    expect($column->getDataValidationRules())->toBe([]);

    $rules = ['statuses.*' => $column->getNestedRecursiveDataValidationRules()];

    expect(Validator::make(['statuses' => ['draft', 'published']], $rules)->fails())->toBeFalse();
    expect(Validator::make(['statuses' => ['draft', 'unknown']], $rules)->fails())->toBeTrue();
});

<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(TestCase::class);

trait TracksImporterLifecycleHooks
{
    protected function beforeValidateTracksImporterLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'beforeValidateTracksImporterLifecycleHooks';
    }

    protected function afterSaveTracksImporterLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSaveTracksImporterLifecycleHooks';
    }
}

class PlainTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

class FormulaSafeTestImporter extends Importer
{
    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

enum ImporterTestStatus: string
{
    case Draft = 'draft';

    case Published = 'published';
}

enum ImporterTestPureStatus
{
    case Draft;
}

class EnumTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('status')
                ->enum(ImporterTestStatus::class),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

class DynamicEnumTestImporter extends Importer
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

class ImporterWithTraitHooks extends Importer
{
    use TracksImporterLifecycleHooks;

    /**
     * @var array<string>
     */
    public array $lifecycleHookInvocations = [];

    public static function getColumns(): array
    {
        return [];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }

    public function resolveRecord(): ?Model
    {
        return new User;
    }

    public function saveRecord(): void {}

    protected function afterSave(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSave';
    }
}

// Reset the base default after every test so the global toggle cannot leak.
afterEach(fn () => Importer::preventFormulaInjection(false));

it('calls trait lifecycle hooks alongside `Importer` hooks', function (): void {
    $importer = new ImporterWithTraitHooks(new Import, [], []);

    $importer([]);

    expect($importer->lifecycleHookInvocations)->toBe([
        'beforeValidateTracksImporterLifecycleHooks',
        'afterSave',
        'afterSaveTracksImporterLifecycleHooks',
    ]);
});

describe('enum columns', function (): void {
    $downloadExampleCsv = static function (string $importer): Reader {
        $response = ImportAction::make()
            ->importer($importer)
            ->livewire(new class extends Component {})
            ->getModalAction('downloadExample')
            ->call();

        expect($response)->toBeInstanceOf(StreamedResponse::class);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $reader = Reader::createFromString($content);
        $reader->setHeaderOffset(0);

        return $reader;
    };

    it('uses enum backing values as examples by default', function (): void {
        $column = ImportColumn::make('status')
            ->enum(ImporterTestStatus::class);

        expect($column->getExamples())->toBe(['draft', 'published']);
    });

    it('keeps `examples()` when it is used with `enum()`', function (): void {
        $column = ImportColumn::make('status')
            ->enum(ImporterTestStatus::class)
            ->examples(['draft']);

        expect($column->getExamples())->toBe(['draft']);
    });

    it('allows `examples([])` to prevent enum example rows from being generated', function (): void {
        $column = ImportColumn::make('status')
            ->enum(ImporterTestStatus::class)
            ->examples([]);

        expect($column->getExamples())->toBe([]);
    });

    it('writes enum backing values to the example CSV', function () use ($downloadExampleCsv): void {
        $reader = $downloadExampleCsv(EnumTestImporter::class);

        expect(iterator_to_array($reader->getRecords()))->toBe([
            1 => ['status' => 'draft'],
            2 => ['status' => 'published'],
        ]);
    });

    it('does not evaluate a dynamic `enum()` when writing the example CSV', function () use ($downloadExampleCsv): void {
        $reader = $downloadExampleCsv(DynamicEnumTestImporter::class);

        expect($reader->getHeader())->toBe(['status'])
            ->and(iterator_to_array($reader->getRecords()))->toBe([]);
    });

    it('validates the state against the enum', function (): void {
        $column = ImportColumn::make('status')
            ->enum(ImporterTestStatus::class);

        $rules = ['status' => $column->getDataValidationRules()];

        expect(Validator::make(['status' => 'draft'], $rules)->fails())->toBeFalse();
        expect(Validator::make(['status' => 'unknown'], $rules)->fails())->toBeTrue();
    });

    it('rejects a pure enum', function (): void {
        expect(
            static fn (): array => ImportColumn::make('status')
                ->enum(ImporterTestPureStatus::class)
                ->getDataValidationRules(),
        )->toThrow(InvalidArgumentException::class, 'must be a backed enum');
    });

    it('does not validate against the enum when `enum()` is not used', function (): void {
        $column = ImportColumn::make('status');

        expect($column->getDataValidationRules())->toBe([]);
    });

    it('accepts a `Closure` for `enum()`', function (): void {
        $column = ImportColumn::make('status')
            ->enum(static fn (): string => ImporterTestStatus::class);

        expect($column->getEnum())->toBe(ImporterTestStatus::class)
            ->and($column->getExamples())->toBe([]);
    });

    it('evaluates a dynamic `enum()` with the importer while validating without evaluating it for example data', function (): void {
        $importer = new DynamicEnumTestImporter(
            new Import,
            ['status' => 'status'],
            ['enum' => ImporterTestStatus::class],
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
            ->enum(ImporterTestStatus::class);

        expect($column->getDataValidationRules())->toBe([]);

        $rules = ['statuses.*' => $column->getNestedRecursiveDataValidationRules()];

        expect(Validator::make(['statuses' => ['draft', 'published']], $rules)->fails())->toBeFalse();
        expect(Validator::make(['statuses' => ['draft', 'unknown']], $rules)->fails())->toBeTrue();
    });
});

describe('`shouldPreventFormulaInjection()`', function (): void {
    it('defaults to `false`', function (): void {
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeFalse();
    });

    it('can be enabled for a single importer via the `$shouldPreventFormulaInjection` property', function (): void {
        expect(FormulaSafeTestImporter::shouldPreventFormulaInjection())->toBeTrue();
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeFalse();
    });

    it('can be enabled globally with `Importer::preventFormulaInjection()`', function (): void {
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeFalse();

        Importer::preventFormulaInjection();

        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeTrue();
    });
});

describe('failure CSV formula injection', function (): void {
    $downloadFailureCsv = function (string $importer): string {
        $user = User::factory()->create();
        $this->actingAs($user);

        $import = Import::create([
            'user_id' => $user->getKey(),
            'file_name' => 'products.csv',
            'file_path' => 'products.csv',
            'importer' => $importer,
            'total_rows' => 1,
        ]);

        $import->failedRows()->create([
            'data' => ['name' => '=1+1', 'price' => '-5', 'phone' => '+44 1234 567890'],
            'validation_error' => 'Invalid',
        ]);

        $url = URL::signedRoute('filament.imports.failed-rows.download', [
            'import' => $import,
            'authGuard' => 'web',
        ], absolute: false);

        $response = $this->get($url);
        $response->assertOk();

        return $response->streamedContent();
    };

    it('leaves formula triggers unescaped by default', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, PlainTestImporter::class);

        expect($content)->toContain('=1+1');
        expect($content)->not->toContain("'=1+1");
        expect($content)->not->toContain("'-5");
    });

    it('escapes formula triggers when `shouldPreventFormulaInjection()` is enabled', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, FormulaSafeTestImporter::class);

        expect($content)->toContain("'=1+1");
        expect($content)->toContain("'+44 1234 567890");
    });

    it('leaves purely numeric strings unescaped when `shouldPreventFormulaInjection()` is enabled', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, FormulaSafeTestImporter::class);

        expect($content)->not->toContain("'-5");
    });
});

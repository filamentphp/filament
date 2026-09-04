<?php

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

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

class FormulaUnsafeTestImporter extends Importer
{
    protected static bool $shouldPreventFormulaInjection = false;

    public static function getColumns(): array
    {
        return [];
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
afterEach(fn () => Importer::preventFormulaInjection(true));

it('calls trait lifecycle hooks alongside `Importer` hooks', function (): void {
    $importer = new ImporterWithTraitHooks(new Import, [], []);

    $importer([]);

    expect($importer->lifecycleHookInvocations)->toBe([
        'beforeValidateTracksImporterLifecycleHooks',
        'afterSave',
        'afterSaveTracksImporterLifecycleHooks',
    ]);
});

describe('`shouldPreventFormulaInjection()`', function (): void {
    it('defaults to `true`', function (): void {
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeTrue();
    });

    it('can be disabled for a single importer via the `$shouldPreventFormulaInjection` property', function (): void {
        expect(FormulaUnsafeTestImporter::shouldPreventFormulaInjection())->toBeFalse();
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeTrue();
    });

    it('can be disabled globally with `Importer::preventFormulaInjection(false)`', function (): void {
        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeTrue();

        Importer::preventFormulaInjection(false);

        expect(PlainTestImporter::shouldPreventFormulaInjection())->toBeFalse();
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

    it('escapes formula triggers by default', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, PlainTestImporter::class);

        expect($content)->toContain("'=1+1");
        expect($content)->toContain("'+44 1234 567890");
    });

    it('leaves purely numeric strings unescaped by default', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, PlainTestImporter::class);

        expect($content)->not->toContain("'-5");
    });

    it('does not escape formula triggers when disabled with `preventFormulaInjection(false)`', function () use ($downloadFailureCsv): void {
        $content = $downloadFailureCsv->call($this, FormulaUnsafeTestImporter::class);

        expect($content)->toContain('=1+1');
        expect($content)->not->toContain("'=1+1");
        expect($content)->not->toContain("'+44 1234 567890");
    });
});

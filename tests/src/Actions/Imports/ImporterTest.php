<?php

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\URL;

uses(TestCase::class);

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

// Reset the base default after every test so the global toggle cannot leak.
afterEach(fn () => Importer::preventFormulaInjection(false));

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

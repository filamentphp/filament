<?php

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\TestCase;

uses(TestCase::class);

// Concrete test subclass to test abstract Exporter
class TestPostExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title'),
            ExportColumn::make('content'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export completed';
    }
}

describe('`getModel()` logic', function (): void {
    it('auto-generates model class from exporter class name', function (): void {
        // TestPostExporter → removes "Exporter" → "TestPost" → App\Models\TestPost
        $model = TestPostExporter::getModel();

        expect($model)->toBeString();
        expect($model)->toContain('Models');
        expect($model)->toContain('TestPost');
    });
});

describe('`getFileDisk()` logic', function (): void {
    it('returns `local` when default disk is `public` and `local` disk exists', function (): void {
        Config::set('filament.default_filesystem_disk', 'public');

        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getFileDisk())->toBe('local');
    });

    it('returns config default when not `public`', function (): void {
        Config::set('filament.default_filesystem_disk', 's3');

        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getFileDisk())->toBe('s3');
    });
});

describe('`getCsvDelimiter()`', function (): void {
    it('returns `,` by default', function (): void {
        expect(TestPostExporter::getCsvDelimiter())->toBe(',');
    });
});

describe('`getFormats()`', function (): void {
    it('returns CSV and XLSX by default', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        $formats = $exporter->getFormats();

        expect($formats)->toHaveCount(2);
        expect($formats[0])->toBe(ExportFormat::Csv);
        expect($formats[1])->toBe(ExportFormat::Xlsx);
    });
});

describe('`getOptionsFormComponents()`', function (): void {
    it('returns empty array by default', function (): void {
        expect(TestPostExporter::getOptionsFormComponents())->toBe([]);
    });
});

describe('`getOptions()`', function (): void {
    it('returns the options passed to constructor', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], ['key' => 'value']);

        expect($exporter->getOptions())->toBe(['key' => 'value']);
    });
});

describe('job configuration', function (): void {
    it('returns backoff array from `getJobBackoff()`', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getJobBackoff())->toBe([60, 120, 300, 600]);
    });

    it('returns `null` from `getJobQueue()` by default', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getJobQueue())->toBeNull();
    });

    it('returns `null` from `getJobConnection()` by default', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getJobConnection())->toBeNull();
    });

    it('returns `null` from `getJobBatchName()` by default', function (): void {
        $export = Mockery::mock(Export::class);
        $exporter = new TestPostExporter($export, [], []);

        expect($exporter->getJobBatchName())->toBeNull();
    });
});

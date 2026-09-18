<?php

use Filament\Actions\Exports\ContentGenerators\CsvExportContentGenerator;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class);

it('yields the CSV header and data chunks', function (): void {
    Storage::fake('local');

    $export = app(Export::class);
    $export->id = 1;
    $export->file_disk = 'local';

    $directory = $export->getFileDirectory();
    $disk = Storage::disk('local');

    $disk->put($directory . DIRECTORY_SEPARATOR . 'headers.csv', "id,name\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . '0000000000000001.csv', "1,Alpha\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . '0000000000000002.csv', "2,Beta\n");
    $disk->put($directory . DIRECTORY_SEPARATOR . 'ignored.txt', 'ignored');

    expect(iterator_to_array(app(CsvExportContentGenerator::class)($export)))->toBe([
        "id,name\n",
        "1,Alpha\n",
        "2,Beta\n",
    ]);
});

<?php

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\Exports\Downloaders\CsvDownloader;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Exports\WorkerPostExporter;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader as CsvReader;
use League\Flysystem\UnableToWriteFile;
use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

uses(TestCase::class);

it('rejects a failed export file write and can execute the original serialized payload after recovery', function (string $stage, bool $throws): void {
    app()->bind(Authenticatable::class, User::class);
    Exceptions::fake();
    $disk = Storage::fake('local');
    $export = Export::create([
        'user_id' => auth()->id(),
        'exporter' => WorkerPostExporter::class,
        'file_disk' => 'local',
        'file_name' => 'posts',
        'total_rows' => 7,
        'processed_rows' => 2,
        'successful_rows' => 1,
    ]);
    $posts = collect(['First, quoted', 'Second', 'Broken', 'Fourth', 'Last'])
        ->map(static fn (string $title): Post => Post::factory()->create(['title' => $title]));
    $query = EloquentSerializeFacade::serialize(Post::query()->orderBy('id'));
    $columnMap = ['title' => 'Post title'];
    $directory = $export->getFileDirectory();
    $path = $directory . '/' . match ($stage) {
        'header' => 'headers.csv',
        'chunk' => '0000000000000003.csv',
        'xlsx' => 'posts.xlsx',
    };
    if ($stage === 'xlsx') {
        $disk->put($directory . '/headers.csv', "\xEF\xBB\xBF\"Post title\"\n");
        $disk->put($directory . '/0000000000000001.csv', "\"First, quoted\"\nSecond\nFourth\nLast\n");
    }
    $job = match ($stage) {
        'header' => (new PrepareCsvExport($export, $query, $columnMap, records: []))->withFakeBatch()[0],
        'chunk' => new ExportCsv($export, $query, $posts->pluck('id')->all(), 3, $columnMap),
        'xlsx' => new CreateXlsxFile($export, $columnMap),
    };
    $payload = serialize($job);
    $failingDisk = Mockery::mock($disk);
    $write = $failingDisk->shouldReceive($stage === 'xlsx' ? 'putFileAs' : 'put');
    $temporaryFile = null;
    $write->andReturnUsing(function (...$arguments) use ($stage, $throws, $path, &$temporaryFile): bool {
        if ($stage === 'xlsx') {
            $temporaryFile = $arguments[1]->getPathname();
        }
        if ($throws) {
            throw UnableToWriteFile::atLocation($path);
        }

        return false;
    });
    Storage::set('local', $failingDisk);

    try {
        expect(fn () => unserialize($payload)->handle())->toThrow(UnableToWriteFile::class);
        expect($export->refresh()->processed_rows)->toBe(2)
            ->and($export->successful_rows)->toBe(1)
            ->and($disk->exists($path))->toBeFalse();
        if ($stage === 'chunk') {
            expect(auth()->guard()->hasUser())->toBeFalse();
        }
        if ($temporaryFile !== null) {
            expect(file_exists($temporaryFile))->toBeFalse();
        }

        Storage::set('local', $disk);
        unserialize($payload)->handle();

        expect($export->refresh()->processed_rows)->toBe($stage === 'chunk' ? 7 : 2)
            ->and($export->successful_rows)->toBe($stage === 'chunk' ? 5 : 1)
            ->and($disk->exists($path))->toBeTrue()
            ->and($disk->getVisibility($path))->toBe('private');
        if ($stage === 'xlsx') {
            $reader = new XlsxReader;
            $reader->open($disk->path($path));
            $rows = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rows[] = $row->toArray();
                }
            }
            $reader->close();
        } else {
            $rows = array_values(iterator_to_array(CsvReader::fromString($disk->get($path))->getRecords()));
        }
        expect($rows)->toBe(match ($stage) {
            'header' => [['Post title']],
            'chunk' => [['First, quoted'], ['Second'], ['Fourth'], ['Last']],
            'xlsx' => [['Post title'], ['First, quoted'], ['Second'], ['Fourth'], ['Last']],
        });
    } finally {
        Storage::set('local', $disk);
        if (($temporaryFile !== null) && file_exists($temporaryFile)) {
            unlink($temporaryFile);
        }
    }
})->with(['header', 'chunk', 'xlsx'])->with(['returns false' => false, 'throws' => true]);

it('preserves CSV cells and records in exports', function (string $format, bool $preventFormulaInjection): void {
    app()->bind(Authenticatable::class, User::class);
    $disk = Storage::fake('local');
    $values = ['quote\\",CELL_MARKER', 'trailing,slash\\', "line one\nline two", 'ordinary', "\tINERT_MARKER"];
    $posts = collect($values)->map(static fn (string $title): Post => Post::factory()->create([
        'title' => $title,
        'content' => 'NEIGHBOR_MARKER',
    ]));
    $export = Export::create([
        'user_id' => auth()->id(),
        'exporter' => WorkerPostExporter::class,
        'file_disk' => 'local',
        'file_name' => 'posts',
        'total_rows' => count($values),
    ]);
    $query = EloquentSerializeFacade::serialize(Post::query()->orderBy('id'));
    $columnMap = ['title' => 'Title', 'content' => 'Body\\",HEADER_MARKER,slash\\'];

    (new PrepareCsvExport($export, $query, $columnMap, records: []))->withFakeBatch()[0]->handle();
    ExportColumn::configureUsing(
        static fn (ExportColumn $column) => $column->preventFormulaInjection($preventFormulaInjection),
        during: fn () => (new ExportCsv($export, $query, $posts->pluck('id')->all(), 1, $columnMap))->handle(),
    );
    if ($preventFormulaInjection) {
        $values[4] = "'\tINERT_MARKER";
    }
    $expected = [array_values($columnMap), ...array_map(static fn (string $value): array => [$value, 'NEIGHBOR_MARKER'], $values)];

    if ($format === 'csv') {
        $response = app(CsvDownloader::class)($export);
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $reader = CsvReader::fromString($content);
        $reader->setEscape('');

        expect(str_starts_with($content, "\xEF\xBB\xBF"))->toBeTrue()
            ->and(array_values(iterator_to_array($reader->getRecords())))->toBe($expected);

        return;
    }

    (new CreateXlsxFile($export, $columnMap))->handle();

    $reader = new XlsxReader;
    $reader->open($disk->path($export->getFileDirectory() . '/posts.xlsx'));
    $rows = [];

    try {
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }
    } finally {
        $reader->close();
    }

    expect($rows)->toBe($expected);
})->with(['csv', 'xlsx'])->with([false, true]);

it('applies `preventFormulaInjection()` to native strings and `AsStringable` attributes in export files', function (string $format, bool $preventFormulaInjection): void {
    app()->bind(Authenticatable::class, User::class);
    $disk = Storage::fake('local');
    $posts = collect(['=2+3', 'Ordinary text', '+42', '-5'])
        ->map(static fn (string $value): Post => Post::factory()->create([
            'title' => $value,
            'content' => $value,
        ]));
    $export = Export::create([
        'user_id' => auth()->id(),
        'exporter' => WorkerPostExporter::class,
        'file_disk' => 'local',
        'file_name' => 'posts',
        'total_rows' => $posts->count(),
    ]);
    $query = EloquentSerializeFacade::serialize(Post::query()->withCasts(['content' => AsStringable::class])->orderBy('id'));
    $columnMap = ['title' => 'Native string', 'content' => 'Stringable'];

    (new PrepareCsvExport($export, $query, $columnMap, records: []))->withFakeBatch()[0]->handle();
    ExportColumn::configureUsing(
        static fn (ExportColumn $column) => $column->preventFormulaInjection($preventFormulaInjection),
        during: fn () => (new ExportCsv($export, $query, $posts->pluck('id')->all(), 1, $columnMap))->handle(),
    );

    $formula = $preventFormulaInjection ? "'=2+3" : '=2+3';
    $expected = [
        ['Native string', 'Stringable'],
        [$formula, $formula],
        ['Ordinary text', 'Ordinary text'],
        ['+42', '+42'],
        ['-5', '-5'],
    ];

    if ($format === 'csv') {
        $response = app(CsvDownloader::class)($export);
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        expect(array_values(iterator_to_array(CsvReader::fromString($content)->getRecords())))->toBe($expected);

        return;
    }

    (new CreateXlsxFile($export, $columnMap))->handle();

    $reader = new XlsxReader;
    $reader->open($disk->path($export->getFileDirectory() . '/posts.xlsx'));
    $rows = [];
    $formulaCellCount = 0;

    try {
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                foreach ($row->getCells() as $cell) {
                    if ($cell instanceof FormulaCell) {
                        $formulaCellCount++;
                    }
                }

                $rows[] = $row->toArray();
            }
        }
    } finally {
        $reader->close();
    }

    expect($formulaCellCount)->toBe($preventFormulaInjection ? 0 : 2)
        ->and($rows)->toBe($expected);
})->with(['csv', 'xlsx'])->with([false, true]);

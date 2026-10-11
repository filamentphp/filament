<?php

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Exports\WorkerPostExporter;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader as CsvReader;
use League\Flysystem\UnableToWriteFile;
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
    $write = $failingDisk->shouldReceive($stage === 'xlsx' ? 'putFileAs' : 'put')->once();
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

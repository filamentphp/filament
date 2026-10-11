<?php

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Jobs\ExportCsv;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Exports\ExportActions;
use Filament\Tests\Fixtures\Exports\ExportTable;
use Filament\Tests\Fixtures\Exports\WorkerExportActions;
use Filament\Tests\Fixtures\Exports\WorkerTestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use League\Csv\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Symfony\Component\Process\Process;

use function Filament\Tests\livewire;

if (getenv('EXPORT_WORKER_DIRECTORY') !== false) {
    WorkerTestCase::validateWorkerEnvironment();
    uses(WorkerTestCase::class);
}

it('exports through separate serialized database worker processes', function (): void {
    $directory = getenv('EXPORT_WORKER_DIRECTORY');

    if (! $directory) {
        $directory = realpath(sys_get_temp_dir()) . '/filament-export-worker-' . bin2hex(random_bytes(12));
        $filesystem = new Filesystem;
        $filesystem->makeDirectory($directory . '/storage/framework/views', 0700, recursive: true);
        $filesystem->makeDirectory($directory . '/storage/framework/cache', 0700, recursive: true);
        touch($directory . '/database.sqlite');
        file_put_contents($directory . '/key', 'base64:' . base64_encode(random_bytes(32)));

        try {
            foreach (['enqueue', 'worker', 'verify'] as $phase) {
                $process = new Process([PHP_BINARY, 'vendor/bin/pest', '--configuration=phpunit.sqlite.xml', '--no-logging', '--do-not-cache-result', __FILE__], dirname(__DIR__, 4), [
                    'EXPORT_WORKER_DIRECTORY' => $directory,
                    'EXPORT_WORKER_PHASE' => $phase,
                    'DB_CONNECTION' => 'testing',
                    'DB_DATABASE' => $directory . '/database.sqlite',
                    'TEST_TOKEN' => false,
                ]);
                $process->setTimeout(120)->run();
                expect($process->isSuccessful())->toBeTrue($phase . ":\n" . $process->getOutput() . $process->getErrorOutput() .
                    ((! $process->isSuccessful() && is_file($directory . '/worker.log')) ? file_get_contents($directory . '/worker.log') : ''));
            }

            $processIds = array_map(static fn (string $phase): int => (int) file_get_contents($directory . '/' . $phase . '.pid'), ['enqueue', 'worker', 'verify']);
            expect(array_unique($processIds))->toHaveCount(3);
        } finally {
            $filesystem->deleteDirectory($directory);
        }

        expect(is_dir($directory))->toBeFalse();

        return;
    }

    $phase = getenv('EXPORT_WORKER_PHASE');
    $connection = DB::connection();
    expect($connection->getConfig('driver'))->toBe('sqlite')
        ->and(realpath($connection->getConfig('database')))->toBe($directory . '/database.sqlite');
    file_put_contents($directory . '/' . $phase . '.pid', (string) getmypid());

    if ($phase === 'enqueue') {
        Artisan::call('migrate', ['--force' => true]);
        $owner = User::factory()->create();
        User::factory()->create();
        $this->actingAs($owner);

        foreach ([
            ['First, "quoted"', "line one\nline two"],
            ['東京', 'café'],
            ['Broken', 'must not appear'],
            ['Fourth', 'semicolon; and comma,'],
            ['Last', 'Ω'],
        ] as [$title, $content]) {
            Post::factory()->create(compact('title', 'content') + ['rating' => 4]);
        }

        foreach ([[ExportFormat::Csv, ExportFormat::Xlsx], [ExportFormat::Xlsx]] as $formats) {
            ExportActions::$formats = $formats;
            livewire(WorkerExportActions::class)->callAction('export', data: [
                'columnMap' => [
                    'title' => ['isEnabled' => true, 'label' => 'Post title'],
                    'content' => ['isEnabled' => true, 'label' => 'Body'],
                ],
            ])->assertHasNoErrors();
        }

        $excluded = Post::factory()->create(['title' => 'Excluded', 'rating' => 9]);
        livewire(ExportTable::class)
            ->selectTableRecords([Post::query()->first()->id, $excluded->id])
            ->callAction(TestAction::make('export')->table()->bulk())
            ->assertHasNoActionErrors();

        expect(Export::query()->pluck('total_rows')->all())->toBe([5, 5, 2])
            ->and(Export::query()->sum('processed_rows'))->toBe(0)
            ->and(DB::table('jobs')->count())->toBe(3)
            ->and(DB::table('notifications')->count())->toBe(0);
        foreach (DB::table('jobs')->pluck('payload') as $payload) {
            expect(json_decode($payload, true)['data']['command'])->toContain(PrepareCsvExport::class);
        }

        return;
    }

    if ($phase === 'worker') {
        Queue::after(static function (JobProcessed $event) use ($directory): void {
            $job = $event->job->payload();
            $name = $event->job->resolveName();
            if (! in_array($name, [PrepareCsvExport::class, ExportCsv::class, CreateXlsxFile::class, ExportCompletion::class])) {
                return;
            }
            $command = unserialize($job['data']['command']);
            $export = (fn (): Export => $this->export)->call($command);
            file_put_contents($directory . '/jobs.jsonl', json_encode([
                'name' => $name,
                'export' => $export->getKey(),
                'serialized' => is_string($job['data']['command']),
                'xlsxExists' => $export->getFileDisk()->exists($export->getFileDirectory() . '/' . $export->file_name . '.xlsx'),
            ]) . "\n", FILE_APPEND);
        });
        $exitCode = Artisan::call('queue:work', ['connection' => 'database', '--queue' => 'exports', '--stop-when-empty' => true, '--sleep' => 0, '--tries' => 1]);
        $failedJobs = DB::table('failed_jobs')->get(['queue', 'exception']);
        $diagnostics = Artisan::output() . "\n" . $failedJobs->toJson();
        expect($exitCode)->toBe(0, $diagnostics)
            ->and(DB::table('jobs')->count())->toBe(0, $diagnostics)
            ->and($failedJobs)->toHaveCount(0, $diagnostics);

        return;
    }

    expect($phase)->toBe('verify');
    $events = array_map(static fn (string $line): array => json_decode($line, true), file($directory . '/jobs.jsonl', FILE_IGNORE_NEW_LINES));
    expect(array_unique(array_column($events, 'serialized')))->toBe([true]);
    $jobNames = array_column($events, 'name');
    expect(array_count_values($jobNames)[PrepareCsvExport::class])->toBe(3)
        ->and(array_count_values($jobNames)[ExportCsv::class])->toBe(7);

    $expected = [
        ['Post title', 'Body'],
        ['Last', 'Ω'],
        ['Fourth', 'semicolon; and comma,'],
        ['東京', 'café'],
        ['First, "quoted"', "line one\nline two"],
    ];
    $owner = User::query()->orderBy('id')->first();
    $other = User::query()->orderBy('id')->skip(1)->first();

    foreach (Export::query()->orderBy('id')->get() as $index => $export) {
        $exportEvents = array_values(array_filter($events, static fn (array $event): bool => $event['export'] === $export->getKey()));
        expect(array_column($exportEvents, 'name'))->toBe([
            PrepareCsvExport::class,
            ...array_fill(0, $index < 2 ? 3 : 1, ExportCsv::class),
            ...match ($index) {
                0 => [ExportCompletion::class, CreateXlsxFile::class],
                1 => [CreateXlsxFile::class, ExportCompletion::class],
                2 => [ExportCompletion::class],
            },
        ]);
        $completion = array_values(array_filter($exportEvents, static fn (array $event): bool => $event['name'] === ExportCompletion::class))[0];
        expect($completion['xlsxExists'])->toBe($index === 1);
        expect($export->completed_at)->not->toBeNull()
            ->and($export->total_rows)->toBe($index < 2 ? 5 : 2)
            ->and($export->processed_rows)->toBe($index < 2 ? 5 : 1)
            ->and($export->successful_rows)->toBe($index < 2 ? 4 : 1)
            ->and($export->getFailedRowsCount())->toBe(1);
        $rows = $index < 2 ? $expected : [['Title'], ['First, "quoted"']];

        if ($index < 2) {
            $path = $export->getFileDisk()->path($export->getFileDirectory() . '/selected-posts.xlsx');
            // Check and parse the worker artifact before a downloader can generate a fallback.
            expect(is_file($path))->toBeTrue();
            $reader = new XlsxReader;
            $reader->open($path);
            $xlsxRows = [];
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $xlsxRows[] = $row->toArray();
                }
            }
            $reader->close();
            expect($xlsxRows)->toBe($rows);
        }

        foreach ($index < 2 ? ['csv', 'xlsx'] : ['csv'] as $format) {
            $url = route('filament.exports.download', ['export' => $export, 'format' => $format]);
            $this->actingAs($owner);
            $response = $this->get($url)->assertOk();
            if ($format === 'csv') {
                expect(array_values(iterator_to_array(CsvReader::fromString($response->streamedContent())->getRecords())))->toBe($rows);
            } else {
                expect($response->streamedContent())->toBe(file_get_contents($path));
            }
            $this->actingAs($other)->get($url)->assertForbidden();
            auth()->forgetGuards();
            $this->get($url)->assertUnauthorized();
        }
    }

    $notifications = $owner->notifications()->get();
    expect($notifications)->toHaveCount(3);
    expect($notifications->pluck('data.body')->sort()->values()->all())->toBe(['Exported 1 posts', 'Exported 4 posts', 'Exported 4 posts']);
    foreach ($notifications as $notification) {
        expect($notification->data['iconColor'])->toBe('warning');
    }
});

if (getenv('EXPORT_WORKER_DIRECTORY') === false) {
    it('rejects unsafe worker entrypoints without changing a sentinel database', function (string $scenario, string $rejection): void {
        $directory = realpath(sys_get_temp_dir()) . '/filament-export-worker-' . bin2hex(random_bytes(12));
        $alias = realpath(sys_get_temp_dir()) . '/filament-export-worker-' . bin2hex(random_bytes(12));
        $filesystem = new Filesystem;
        $filesystem->makeDirectory($directory, 0700);

        try {
            $database = new PDO('sqlite:' . $directory . '/database.sqlite');
            $database->exec('CREATE TABLE sentinel (value TEXT)');
            $database->exec("INSERT INTO sentinel VALUES ('untouched')");
            $database = null;
            $hash = hash_file('sha256', $directory . '/database.sqlite');
            $phase = 'worker';
            $childDirectory = $directory;

            switch ($scenario) {
                case 'phase':
                    $phase = 'invalid';

                    break;
                case 'populated enqueue':
                    $phase = 'enqueue';

                    break;
                case 'noncanonical directory':
                    $childDirectory = $directory . '/.';

                    break;
                case 'directory symlink':
                    symlink($directory, $alias);
                    $childDirectory = $alias;

                    break;
                case 'database symlink':
                    rename($directory . '/database.sqlite', $directory . '/sentinel.sqlite');
                    symlink($directory . '/sentinel.sqlite', $directory . '/database.sqlite');

                    break;
            }

            $process = new Process([PHP_BINARY, 'vendor/bin/pest', '--configuration=phpunit.sqlite.xml', '--no-logging', '--do-not-cache-result', __FILE__], dirname(__DIR__, 4), [
                'EXPORT_WORKER_DIRECTORY' => $childDirectory,
                'EXPORT_WORKER_PHASE' => $phase,
                'DB_CONNECTION' => 'testing',
                'DB_DATABASE' => $directory . '/database.sqlite',
                'TEST_TOKEN' => false,
            ]);
            $process->setTimeout(30)->run();
            expect($process->isSuccessful())->toBeFalse()
                ->and($process->getOutput() . $process->getErrorOutput())->toContain('Rejected export worker ' . $rejection . '.')
                ->and(hash_file('sha256', $directory . '/database.sqlite'))->toBe($hash)
                ->and(glob($directory . '/*.pid'))->toBe([]);
        } finally {
            if (is_link($alias)) {
                unlink($alias);
            }
            $filesystem->deleteDirectory($directory);
        }
    })->with([
        ['phase', 'phase'],
        ['populated enqueue', 'database'],
        ['noncanonical directory', 'directory'],
        ['directory symlink', 'directory'],
        ['database symlink', 'database'],
    ]);
}

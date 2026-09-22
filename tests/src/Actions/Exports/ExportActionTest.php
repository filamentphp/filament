<?php

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Exports\ActionPostExporter;
use Filament\Tests\Fixtures\Exports\ExportActions;
use Filament\Tests\Fixtures\Exports\ExportTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Bus\ChainedBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Reader\XLSX\Reader;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    app()->instance(Authenticatable::class, auth()->user());
    Storage::fake('local');
    config()->set('queue.default', 'sync');
    ActionPostExporter::$getterCalls = [];
    ExportActions::$authorized = true;
    ExportActions::$formats = [ExportFormat::Csv];
    ExportActions::$guard = null;
    ExportActions::$maxRows = null;
});

it('submits a real form and completes a synchronous export using the configured guard', function (array $formats): void {
    ExportActions::$formats = $formats;
    ExportActions::$guard = 'export';
    config()->set('auth.guards.export', ['driver' => 'session', 'provider' => 'users']);
    $exportUser = User::factory()->create();
    auth('export')->setUser($exportUser);
    expect(auth()->id())->not->toBe($exportUser->id);
    Post::factory()->create(['title' => 'Included', 'rating' => 3]);
    Post::factory()->create(['title' => 'Too low', 'rating' => 1]);
    Post::factory()->create(['title' => 'Too high', 'rating' => 9]);

    livewire(ExportActions::class)
        ->callAction('export', data: ['columnMap' => ['title' => ['isEnabled' => true, 'label' => 'Post title']]])
        ->assertHasNoErrors();

    $export = Export::query()->sole();
    expect($export->total_rows)->toBe(1)
        ->and($export->user_id)->toBe($exportUser->id)
        ->and($export->processed_rows)->toBe(1)
        ->and($export->successful_rows)->toBe(1)
        ->and($export->completed_at)->not->toBeNull()
        ->and($export->file_name)->toBe('selected-posts')
        ->and($export->file_disk)->toBe('local');
    $files = Storage::disk('local')->allFiles($export->getFileDirectory());
    expect($files)->not->toBeEmpty();
    $contents = implode('', array_map(static fn (string $file): string => str_ends_with($file, '.csv') ? Storage::disk('local')->get($file) : '', $files));
    expect($contents)->toContain('Post title', 'Included')->not->toContain('Too low', 'Too high');
    expect(ActionPostExporter::$getterCalls)->toBe(['queue', 'connection', 'batch']);
    $notifications = session('filament.claimed_notifications') ?? session('filament.notifications');
    expect($notifications)->toHaveCount(1);
    expect(reset($notifications)['body'])->toBe('Exported 1 posts');
    if (in_array(ExportFormat::Xlsx, $formats)) {
        Storage::disk('local')->assertExists($export->getFileDirectory() . '/selected-posts.xlsx');
        $reader = new Reader;
        $reader->open(Storage::disk('local')->path($export->getFileDirectory() . '/selected-posts.xlsx'));
        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }
        }
        $reader->close();
        expect($rows)->toBe([['Post title'], ['Included']]);
    }
})->with([
    'CSV' => [[ExportFormat::Csv]],
    'XLSX' => [[ExportFormat::Xlsx]],
    'both' => [[ExportFormat::Csv, ExportFormat::Xlsx]],
]);

it('preserves job construction and serialized chain ordering and configuration', function (array $formats, array $expectedChain): void {
    Bus::fake();
    ExportActions::$formats = $formats;
    $constructed = [];
    foreach ([PrepareCsvExport::class, CreateXlsxFile::class, ExportCompletion::class] as $jobClass) {
        app()->resolving($jobClass, function (object $job) use (&$constructed): void {
            $constructed[] = $job::class;
        });
    }

    livewire(ExportActions::class)->callAction('export')->assertHasNoErrors();

    expect($constructed)->toBe([PrepareCsvExport::class, ...$expectedChain]);
    Bus::assertDispatched(ChainedBatch::class, function (ChainedBatch $batch) use ($expectedChain): bool {
        expect($batch->queue)->toBe('exports')
            ->and($batch->connection)->toBe('sync')
            ->and($batch->chainQueue)->toBe('exports')
            ->and($batch->chainConnection)->toBe('sync')
            ->and(array_map(static fn (string $job): string => unserialize($job)::class, $batch->chained))->toBe($expectedChain);
        $restored = unserialize(serialize($batch));
        $payload = (fn (): array => get_object_vars($this))->call($restored->jobs->sole());
        expect($payload['export']->relationLoaded('user'))->toBeFalse();

        return true;
    });
})->with([
    'CSV' => [[ExportFormat::Csv], [ExportCompletion::class]],
    'XLSX' => [[ExportFormat::Xlsx], [CreateXlsxFile::class, ExportCompletion::class]],
    'both' => [[ExportFormat::Csv, ExportFormat::Xlsx], [ExportCompletion::class, CreateXlsxFile::class]],
]);

it('keeps table filters search order and both query modifiers in the preparation payload', function (): void {
    Bus::fake();
    $second = Post::factory()->create(['title' => 'Match B', 'rating' => 5, 'is_published' => true]);
    $first = Post::factory()->create(['title' => 'Match A', 'rating' => 6, 'is_published' => true]);
    Post::factory()->create(['title' => 'Match low', 'rating' => 3, 'is_published' => true]);
    Post::factory()->create(['title' => 'Match high', 'rating' => 9, 'is_published' => true]);
    Post::factory()->create(['title' => 'Match draft', 'rating' => 5, 'is_published' => false]);
    Post::factory()->create(['title' => 'Other', 'rating' => 5, 'is_published' => true]);

    livewire(ExportTable::class)
        ->filterTable('published')
        ->searchTable('Match')
        ->sortTable('title')
        ->callAction(TestAction::make('export')->table(), data: ['minimum' => 5])
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ChainedBatch::class, function (ChainedBatch $batch) use ($first, $second): bool {
        $job = $batch->jobs->sole();
        expect($job)->toBeInstanceOf(PrepareCsvExport::class);
        $payload = (fn (): array => get_object_vars($this))->call($job);
        expect(EloquentSerializeFacade::unserialize($payload['query'])->pluck('id')->all())->toBe([$first->id, $second->id])
            ->and($payload['options'])->toBe(['minimum' => 5.0, 'static' => 'retained'])
            ->and($payload['columnMap'])->toBe(['title' => 'Title'])
            ->and($payload['records'])->toBeNull()
            ->and($batch->name)->toBe('Post export')
            ->and($batch->options)->toMatchArray(['allowFailures' => true, 'queue' => 'exports', 'connection' => 'sync']);

        return true;
    });
});

it('keeps bulk selected IDs separate from the full export query', function (): void {
    Bus::fake();
    $posts = Post::factory()->count(3)->create(['rating' => 4]);
    livewire(ExportTable::class)
        ->selectTableRecords([$posts[1]])
        ->callAction(TestAction::make('export')->table()->bulk())
        ->assertHasNoActionErrors();

    Bus::assertDispatched(ChainedBatch::class, function (ChainedBatch $batch) use ($posts): bool {
        $payload = (fn (): array => get_object_vars($this))->call($batch->jobs->sole());
        expect($payload['records'])->toBe([$posts[1]->id])
            ->and(EloquentSerializeFacade::unserialize($payload['query'])->count())->toBe(3);

        return true;
    });
    expect(Export::query()->sole()->total_rows)->toBe(1);
});

it('rejects empty column selection and authorization revoked after mounting', function (): void {
    Bus::fake();
    livewire(ExportActions::class)
        ->callAction('export', data: ['columnMap' => ['title' => ['isEnabled' => false]]])
        ->assertActionHalted('export');

    $component = livewire(ExportActions::class)->mountAction('export');
    ExportActions::$authorized = false;
    $component->call('callMountedAction');

    expect(Export::query()->count())->toBe(0);
    Bus::assertNothingDispatched();
});

it('enforces `maxRows()` before creating an export and accepts the exact boundary', function (): void {
    Bus::fake();
    Post::factory()->count(2)->create(['rating' => 4]);
    ExportActions::$maxRows = 1;
    livewire(ExportActions::class)->callAction('export')->assertHasNoErrors();
    expect(Export::query()->count())->toBe(0);
    Bus::assertNothingDispatched();

    ExportActions::$maxRows = 2;
    livewire(ExportActions::class)->callAction('export')->assertHasNoErrors();
    expect(Export::query()->sole()->total_rows)->toBe(2);
    Bus::assertDispatchedTimes(ChainedBatch::class, 1);
});

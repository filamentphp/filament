<?php

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportDispatcher;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Exports\ActionPostExporter;
use Filament\Tests\Fixtures\Exports\ExportActions;
use Filament\Tests\Fixtures\Exports\ExportTable;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\AssertionFailedError;

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
    ExportActions::$queryLimit = null;
});

it('records real form preparation but skips custom job construction and file generation', function (): void {
    $fake = ExportAction::fake();
    ExportAction::configureUsing(static fn (ExportAction $action) => $action->job(UnconstructableExportJob::class));
    $included = Post::factory()->create(['rating' => 5]);
    Post::factory()->create(['rating' => 3]);
    Post::factory()->create(['rating' => 9]);
    $saved = [];
    Export::saved(function (Export $export) use (&$saved): void {
        $saved[] = $export->file_name;
        if (! $export->file_name) {
            $export->getFileDisk()->put($export->getFileDirectory() . '/stale.csv', 'stale');
        }
    });

    livewire(ExportActions::class)
        ->callAction('export', data: [
            'minimum' => 5,
            'columnMap' => [
                'title' => ['isEnabled' => true, 'label' => 'Post title'],
                'content' => ['isEnabled' => true, 'label' => 'Hidden content'],
            ],
        ])
        ->assertHasNoErrors();

    expect($saved)->toBe([null, 'selected-posts']);
    $fake->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query, array $columnMap, array $options, array $formats, ?array $records) use ($included): bool {
        expect($export->exists)->toBeTrue()
            ->and($export->total_rows)->toBe(1)
            ->and($export->file_disk)->toBe('local')
            ->and($export->file_name)->toBe('selected-posts')
            ->and($export->user_id)->toBe(auth()->id())
            ->and($export->relationLoaded('user'))->toBeFalse()
            ->and($export->completed_at)->toBeNull()
            ->and($query->pluck('id')->all())->toBe([$included->id])
            ->and($columnMap)->toBe(['title' => 'Post title'])
            ->and($options)->toBe(['minimum' => 5.0, 'static' => 'retained'])
            ->and($formats)->toBe([ExportFormat::Csv])
            ->and($records)->toBeNull();
        expect(Storage::disk('local')->allFiles($export->getFileDirectory()))->toBe([]);

        return true;
    })->assertDispatchedTimes(ActionPostExporter::class);
    expect(ActionPostExporter::$getterCalls)->toBe(['queue', 'connection', 'batch']);
});

it('shares the fake between header and bulk actions and resets it with either entry point', function (): void {
    $fake = ExportBulkAction::fake();
    $posts = Post::factory()->count(3)->create(['rating' => 4]);
    livewire(ExportTable::class)->callAction(TestAction::make('export')->table());
    livewire(ExportTable::class)->selectTableRecords([$posts[1]])->callAction(TestAction::make('export')->table()->bulk());

    $fake->assertDispatchedTimes(ActionPostExporter::class, 2)
        ->assertDispatched(ActionPostExporter::class, static fn (Export $export, Builder $query, array $columnMap, array $options, array $formats, ?array $records): bool => $records === null)
        ->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query, array $columnMap, array $options, array $formats, ?array $records) use ($posts): bool {
            if ($records === null) {
                return false;
            }
            expect($query->orderBy('id')->pluck('id')->all())->toBe($posts->modelKeys())
                ->and($records)->toBe([$posts[1]->id])
                ->and($export->total_rows)->toBe(1);

            return true;
        });

    $replacement = ExportAction::fake();
    expect($replacement)->not->toBe($fake)->toBe(app(ExportDispatcher::class));
    $replacement->assertNothingDispatched();
    livewire(ExportTable::class)->selectTableRecords([$posts[0]])->callAction(TestAction::make('export')->table()->bulk());
    $replacement->assertDispatchedTimes(ActionPostExporter::class);
    $fake->assertDispatchedTimes(ActionPostExporter::class, 2);
    ExportBulkAction::fake()->assertNothingDispatched();
});

it('deserializes a fresh query for every callback without applying selection or loading records', function (): void {
    $fake = ExportAction::fake();
    $post = Post::factory()->create(['rating' => 4]);
    $export = new Export(['exporter' => ActionPostExporter::class]);
    $fake->dispatch($export, EloquentSerializeFacade::serialize(Post::query()->whereKey($post->id)), [], [], [], [], PrepareCsvExport::class, 100, null, null, null, 'web');

    DB::enableQueryLog();
    DB::flushQueryLog();
    $fake->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query) use ($post): bool {
        expect($query->getModel())->toBeInstanceOf(Post::class)
            ->and($query->getQuery()->from)->toBe('posts')
            ->and($query->getBindings())->toBe([$post->id]);

        return true;
    });
    expect(DB::getQueryLog())->toBe([]);
    DB::disableQueryLog();

    $fake->assertDispatched(ActionPostExporter::class, function (Export $actualExport, Builder $query, array $columnMap, array $options, array $formats, ?array $records) use ($export): bool {
        expect($actualExport)->toBe($export)->and($records)->toBe([]);
        $query->whereRaw('1 = 0');
        expect($query->count())->toBe(0);

        return true;
    });
    $fake->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query) use ($post): bool {
        expect($query->pluck('id')->all())->toBe([$post->id]);

        return true;
    });

    EloquentSerializeFacade::shouldReceive('unserialize')->never();
    DB::enableQueryLog();
    DB::flushQueryLog();
    $fake->assertDispatched(ActionPostExporter::class)->assertDispatchedTimes(ActionPostExporter::class);
    expect(fn () => $fake->assertNotDispatched(ActionPostExporter::class))->toThrow(AssertionFailedError::class);
    expect(DB::getQueryLog())->toBe([]);
    DB::disableQueryLog();
});

it('fails assertions for the wrong exporter predicate count and unexpected exports', function (): void {
    $fake = ExportAction::fake();
    $fake->assertNothingDispatched()->assertDispatchedTimes(ActionPostExporter::class, 0);
    livewire(ExportActions::class)->callAction('export');

    expect(fn () => $fake->assertDispatched(Exporter::class))->toThrow(AssertionFailedError::class)
        ->and(fn () => $fake->assertDispatched(ActionPostExporter::class, static fn (): bool => false))->toThrow(AssertionFailedError::class)
        ->and(fn () => $fake->assertDispatchedTimes(ActionPostExporter::class, 2))->toThrow(AssertionFailedError::class)
        ->and(fn () => $fake->assertDispatchedTimes(Exporter::class))->toThrow(AssertionFailedError::class)
        ->and(fn () => $fake->assertNothingDispatched())->toThrow(AssertionFailedError::class);
});

it('asserts no matching export was dispatched without rejecting other exports', function (): void {
    $fake = ExportAction::fake();
    expect($fake->assertNotDispatched(ActionPostExporter::class))->toBe($fake);
    Post::factory()->create(['rating' => 4]);
    livewire(ExportActions::class)->callAction('export', data: ['minimum' => 2]);
    livewire(ExportActions::class)->callAction('export', data: ['minimum' => 5]);

    $fake->assertNotDispatched(Exporter::class, static fn (): bool => throw new LogicException('Unrelated exporter callback invoked.'));
    $seenMinimums = [];
    expect($fake->assertNotDispatched(ActionPostExporter::class, function (Export $export, Builder $query, array $columnMap, array $options, array $formats, ?array $records) use (&$seenMinimums): bool {
        $seenMinimums[] = $options['minimum'];
        expect($export->total_rows)->toBe($options['minimum'] === 2.0 ? 1 : 0)
            ->and($query->count())->toBe($export->total_rows)
            ->and($columnMap)->toBe(['title' => 'Title'])
            ->and($formats)->toBe([ExportFormat::Csv])
            ->and($records)->toBeNull();
        $query->whereRaw('1 = 0');

        return false;
    }))->toBe($fake);
    expect($seenMinimums)->toBe([2.0, 5.0]);
    $fake->assertDispatched(ActionPostExporter::class, static fn (Export $export, Builder $query): bool => $query->count() === 1);

    expect(fn () => $fake->assertNotDispatched(ActionPostExporter::class))->toThrow(AssertionFailedError::class)
        ->and(fn () => $fake->assertNotDispatched(ActionPostExporter::class, static fn (Export $export): bool => $export->total_rows === 0))
        ->toThrow(AssertionFailedError::class, 'with matching data.');

    $exception = new LogicException('Callback failed.');

    try {
        $fake->assertNotDispatched(ActionPostExporter::class, static fn (): bool => throw $exception);
    } catch (LogicException $caughtException) {
        expect($caughtException)->toBe($exception);

        return;
    }

    $this->fail('The callback exception was swallowed.');
});

it('does not change unrelated real jobs batches or events or existing bus and event fakes', function (): void {
    $fake = ExportAction::fake();
    UnrelatedExportTestJob::$handled = 0;
    $events = [];
    Event::listen('unrelated-export-test', function (string $value) use (&$events): void {
        $events[] = $value;
    });
    Bus::dispatch(new UnrelatedExportTestJob);
    $batch = Bus::batch([new UnrelatedExportTestJob])->dispatch();
    Event::dispatch('unrelated-export-test', ['delivered']);
    expect(UnrelatedExportTestJob::$handled)->toBe(2)
        ->and($batch->fresh()->finished())->toBeTrue()
        ->and($events)->toBe(['delivered']);
    $fake->assertNothingDispatched();

    $bus = Bus::fake();
    $event = Event::fake();
    ExportBulkAction::fake();
    expect(Bus::getFacadeRoot())->toBe($bus)->and(Event::getFacadeRoot())->toBe($event);
    Bus::dispatch(new UnrelatedExportTestJob);
    Bus::batch([new UnrelatedExportTestJob])->dispatch();
    Event::dispatch('unrelated-export-test');
    Bus::assertDispatched(UnrelatedExportTestJob::class);
    Bus::assertBatchCount(1);
    Event::assertDispatched('unrelated-export-test');
});

it('still validates forms and rejects authorization empty columns and excessive rows', function (): void {
    $fake = ExportAction::fake();
    Post::factory()->count(2)->create(['rating' => 4]);
    livewire(ExportActions::class)->callAction('export', data: ['minimum' => null])->assertHasActionErrors(['minimum' => 'required']);
    livewire(ExportActions::class)->callAction('export', data: ['columnMap' => ['title' => ['isEnabled' => false]]])->assertActionHalted('export');
    $component = livewire(ExportActions::class)->mountAction('export')
        ->assertActionDataSet([
            'minimum' => 2,
            'columnMap' => [
                'title' => ['isEnabled' => true, 'label' => 'Title'],
                'rating' => ['isEnabled' => false, 'label' => 'Rating'],
            ],
        ]);
    ExportActions::$authorized = false;
    $component->call('callMountedAction');
    ExportActions::$authorized = true;
    ExportActions::$maxRows = 1;
    livewire(ExportActions::class)->callAction('export');
    expect(Export::query()->count())->toBe(0);
    $fake->assertNothingDispatched();
});

it('captures table search and renamed columns using the documented callback pattern', function (): void {
    $matching = Post::factory()->create(['title' => 'Oak desk', 'rating' => 4]);
    Post::factory()->create(['title' => 'Walnut chair', 'rating' => 4]);
    $exports = ExportAction::fake();

    livewire(ExportTable::class)
        ->searchTable('Oak')
        ->callAction(TestAction::make('export')->table(), data: [
            'columnMap' => ['title' => ['isEnabled' => true, 'label' => 'Product name']],
        ])
        ->assertHasNoErrors();

    $exports->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query, array $columnMap, array $options, array $formats, ?array $records) use ($matching): bool {
        expect($query->pluck('id')->all())->toBe([$matching->getKey()])
            ->and($columnMap)->toBe(['title' => 'Product name'])
            ->and($formats)->toBe([ExportFormat::Csv])
            ->and($records)->toBeNull()
            ->and($export->total_rows)->toBe(1);

        return true;
    });
});

it('preserves query limits and unmapped column selection with and without visible table defaults', function (bool $visibleColumnsOnly, array $expectedMap): void {
    $fake = ExportAction::fake();
    ExportAction::configureUsing(static fn (ExportAction $action) => $action
        ->columnMapping(false)
        ->enableVisibleTableColumnsByDefault($visibleColumnsOnly));
    Post::factory()->count(3)->create(['rating' => 4]);
    ExportActions::$queryLimit = 1;
    ExportActions::$maxRows = 1;

    livewire(ExportTable::class)->callAction(TestAction::make('export')->table())->assertHasNoErrors();

    $fake->assertDispatched(ActionPostExporter::class, function (Export $export, Builder $query, array $columnMap) use ($expectedMap): bool {
        expect($export->total_rows)->toBe(1)
            ->and($query->getQuery()->limit)->toBe(1)
            ->and($query->get())->toHaveCount(1)
            ->and($columnMap)->toBe($expectedMap);

        return true;
    });
})->with([
    'all visible exporter columns' => [false, ['title' => 'Title', 'rating' => 'Rating']],
    'visible table columns only' => [true, ['title' => 'Title']],
]);

class UnconstructableExportJob extends PrepareCsvExport
{
    public function __construct()
    {
        throw new LogicException('The fake must not construct the preparation job.');
    }
}

class UnrelatedExportTestJob implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public static int $handled = 0;

    public function handle(): void
    {
        static::$handled++;
    }
}

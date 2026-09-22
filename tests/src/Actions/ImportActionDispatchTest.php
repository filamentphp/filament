<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\ImportDispatcher;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use PHPUnit\Framework\AssertionFailedError;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

beforeEach(function (): void {
    app()->bind(Authenticatable::class, User::class);
    DispatchPostImporter::$connectionCalls = 0;
});

it('runs synchronous imports and completion notifications with the original guard and one connection lookup', function (): void {
    Exceptions::fake()->throwOnReport();
    config(['queue.default' => 'database', 'auth.guards.staff' => config('auth.guards.web')]);
    auth('staff')->setUser(auth()->user());
    $events = [];
    Event::listen(ImportStarted::class, function () use (&$events): void {
        $events[] = 'started';
    });
    Event::listen(ImportCompleted::class, function () use (&$events): void {
        $events[] = 'completed';
    });

    livewire(ImportDispatchPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('posts.csv', "title,content\nFirst post,Body\n,Invalid row\n")])
        ->setActionData(['columnMap' => ['title' => 'title', 'content' => 'content']])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $import = Import::query()->sole();
    expect($events)->toBe(['started', 'completed'])
        ->and(DispatchPostImporter::$connectionCalls)->toBe(1)
        ->and($import->processed_rows)->toBe(2)
        ->and($import->successful_rows)->toBe(1)
        ->and($import->completed_at)->not->toBeNull();
    assertDatabaseHas('posts', ['title' => 'First post']);
    assertDatabaseCount('failed_import_rows', 1);

    $notifications = session('filament.claimed_notifications');
    expect($notifications)->toHaveCount(1);
    $notification = array_values($notifications)[0];
    expect($notification['title'])->toBe('Dispatch completed')
        ->and($notification['status'])->toBe('warning')
        ->and($notification['duration'])->toBe('persistent')
        ->and($notification['actions'][0]['url'])->toContain('authGuard=staff');
});

it('preserves importer batch metadata and the asynchronous started notification', function (): void {
    config(['testing.import_connection' => 'database', 'auth.guards.staff' => config('auth.guards.web')]);
    auth('staff')->setUser(auth()->user());
    Bus::fake();

    livewire(ImportDispatchPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('posts.csv', "title,content\nFirst post,Body\n")])
        ->setActionData(['columnMap' => ['title' => 'title', 'content' => 'content']])
        ->callMountedAction()
        ->assertHasNoActionErrors()
        ->assertNotified();

    Bus::assertBatched(static fn (PendingBatch $batch): bool => ($batch->name === 'Post import')
        && ($batch->options['connection'] === 'database')
        && ($batch->options['queue'] === 'imports')
        && $batch->options['allowFailures']);
    Bus::assertBatchCount(1);
    expect(DispatchPostImporter::$connectionCalls)->toBe(1);
    assertDatabaseCount('posts', 0);
});

it('fakes only import dispatch while preserving action metadata and unrelated jobs, batches and events', function (string $connection): void {
    config(['queue.default' => $connection, 'auth.guards.staff' => config('auth.guards.web'), 'testing.import_job' => UnconstructableImportJob::class]);
    $defaultUser = auth()->user();
    $staffUser = User::factory()->create();
    auth('staff')->setUser($staffUser);
    $bus = Bus::getFacadeRoot();
    $events = Event::getFacadeRoot();
    $importEvents = [];
    Event::listen([ImportStarted::class, ImportCompleted::class], function (object $event) use (&$importEvents): void {
        $importEvents[] = $event;
    });
    $probeEvents = 0;
    Event::listen('import-fake-probe', function () use (&$probeEvents): void {
        $probeEvents++;
    });

    $fake = ImportAction::fake();
    expect($fake->assertNothingDispatched())->toBe($fake);

    $page = livewire(ImportDispatchPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('posts.csv', "Headline,Body\nImported title,Imported body\n")])
        ->setActionData(['columnMap' => ['title' => 'Headline', 'content' => 'Body'], 'mode' => 'replace'])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    if ($connection === 'sync') {
        $page->assertNotNotified();
    } else {
        $page->assertNotified();
    }

    $fake->assertDispatched(DispatchPostImporter::class, function (Import $import, array $columnMap, array $options) use ($defaultUser, $staffUser): bool {
        expect($import->exists)->toBeTrue()
            ->and($import->user->is($staffUser))->toBeTrue()
            ->and($import->user->is($defaultUser))->toBeFalse()
            ->and($import->file_name)->toBe('posts.csv')
            ->and($import->total_rows)->toBe(1)
            ->and($columnMap)->toBe(['title' => 'Headline', 'content' => 'Body'])
            ->and($options)->toBe(['source' => 'catalog', 'mode' => 'replace']);

        return true;
    })->assertDispatchedTimes(DispatchPostImporter::class);
    expect($importEvents)->toBe([])
        ->and(DispatchPostImporter::$connectionCalls)->toBe(0);
    assertDatabaseCount('posts', 0);
    assertDatabaseCount('job_batches', 0);
    assertDatabaseHas('imports', ['processed_rows' => 0, 'successful_rows' => 0, 'completed_at' => null]);

    Bus::dispatch((new ImportFakeProbeJob('Standalone job'))->onConnection('sync'));
    $batch = Bus::batch([new ImportFakeProbeJob('Batch job')])->onConnection('sync')->dispatch();
    Event::dispatch('import-fake-probe');
    assertDatabaseHas('posts', ['title' => 'Standalone job']);
    assertDatabaseHas('posts', ['title' => 'Batch job']);
    expect($batch->fresh()->finished())->toBeTrue()
        ->and($probeEvents)->toBe(1)
        ->and(Bus::getFacadeRoot())->toBe($bus)
        ->and(Event::getFacadeRoot())->toBe($events);

    $replacement = ImportAction::fake();
    expect($replacement)->not->toBe($fake)
        ->and(app(ImportDispatcher::class))->toBe($replacement);
    $replacement->assertNothingDispatched();
    $fake->assertDispatchedTimes(DispatchPostImporter::class);
    expect(Bus::getFacadeRoot())->toBe($bus)->and(Event::getFacadeRoot())->toBe($events);
})->with(['sync', 'database']);

it('does not record rejected import forms or unauthorized submissions', function (array $data, array $errors, bool $authorized): void {
    config(['auth.guards.staff' => config('auth.guards.web')]);
    auth('staff')->setUser(auth()->user());
    $fake = ImportAction::fake();

    $page = livewire(ImportDispatchPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('posts.csv', "title,content\nFirst post,Body\n")])
        ->setActionData(['columnMap' => ['title' => 'title', 'content' => 'content'], ...$data])
        ->set('canImport', $authorized)
        ->call('callMountedAction');

    if ($errors) {
        $page->assertHasActionErrors($errors);
    }

    $fake->assertNothingDispatched();
    assertDatabaseCount('imports', 0);
})->with([
    'mapping' => [['columnMap' => ['title' => null]], ['columnMap.title' => 'required'], true],
    'options' => [['mode' => 'invalid'], ['mode' => 'in'], true],
    'authorization' => [[], [], false],
]);

it('asserts matching requests and exact per-importer counts with useful failures', function (): void {
    $fake = ImportAction::fake();
    foreach (['first', 'second'] as $source) {
        $fake->dispatch(app(Import::class, ['attributes' => ['importer' => DispatchPostImporter::class]]), [], ['title' => 'Headline'], ['source' => $source], UnconstructableImportJob::class, null);
    }
    expect(fn () => $fake->assertDispatched(Importer::class))->toThrow(AssertionFailedError::class, Importer::class);
    $fake->dispatch(app(Import::class, ['attributes' => ['importer' => Importer::class]]), [], [], [], UnconstructableImportJob::class, null);

    expect($fake->assertDispatched(DispatchPostImporter::class))->toBe($fake);
    $fake->assertDispatched(DispatchPostImporter::class, static fn (Import $import, array $columnMap, array $options): bool => $options['source'] === 'second')
        ->assertDispatchedTimes(DispatchPostImporter::class, 2)
        ->assertDispatchedTimes(Importer::class);

    expect(fn () => $fake->assertDispatched(DispatchPostImporter::class, static fn (): bool => false))->toThrow(AssertionFailedError::class, 'matching data')
        ->and(fn () => $fake->assertDispatchedTimes(DispatchPostImporter::class))->toThrow(AssertionFailedError::class, '1 time(s)')
        ->and(fn () => $fake->assertNothingDispatched())->toThrow(AssertionFailedError::class, 'Imports were dispatched.');
});

class ImportDispatchPage extends Actions
{
    public bool $canImport = true;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(DispatchPostImporter::class)->authGuard('staff')
                ->job(config('testing.import_job'))
                ->options(['source' => 'catalog', 'mode' => 'static'])
                ->authorize(fn (): bool => $this->canImport),
        ];
    }
}

class DispatchPostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static int $connectionCalls = 0;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->requiredMapping()->rules(['required']),
            ImportColumn::make('content'),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return app(Post::class);
    }

    public static function getOptionsFormComponents(): array
    {
        return [TextInput::make('mode')->default('append')->required()->in(['append', 'replace'])];
    }

    public function getJobConnection(): ?string
    {
        static::$connectionCalls++;

        return config('testing.import_connection', 'sync');
    }

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function getJobBatchName(): ?string
    {
        return 'Post import';
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Dispatch completed';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}

class UnconstructableImportJob
{
    public function __construct()
    {
        throw new LogicException('The import fake must not construct custom jobs.');
    }
}

class ImportFakeProbeJob implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public string $title) {}

    public function handle(): void
    {
        Post::create(['title' => $this->title]);
    }
}

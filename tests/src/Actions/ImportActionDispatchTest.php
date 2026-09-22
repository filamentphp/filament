<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;

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

class ImportDispatchPage extends Actions
{
    protected function getHeaderActions(): array
    {
        return [ImportAction::make()->importer(DispatchPostImporter::class)->authGuard('staff')];
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

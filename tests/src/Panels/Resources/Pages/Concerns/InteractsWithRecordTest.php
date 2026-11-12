<?php

use Filament\Actions\ForceDeleteAction;
use Filament\Facades\Filament;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\RecordChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\Facades\FilamentView;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ViewPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\EditUserPost;
use Filament\Tests\Panels\Resources\TestCase;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    config([
        'filament.broadcasting.echo' => ['broadcaster' => 'null'],
        'broadcasting.default' => 'pusher',
        'broadcasting.connections.pusher' => [
            'driver' => 'pusher',
            'key' => 'record-title-test-key',
            'secret' => 'record-title-test-secret',
            'app_id' => 'record-title-test',
            'options' => ['cluster' => 'mt1'],
        ],
    ]);

    Event::fake([RecordChangedBroadcast::class]);
});

it('automatically enables page updates using the effective model subscription and panel broadcasting', function (): void {
    $post = Post::factory()->create();
    $page = livewire(EditPost::class, ['record' => $post->getKey()])->instance();

    expect($page->hasLiveRecordUpdates())->toBeFalse();

    FilamentRealtime::subscribe()->allModels();
    expect($page->hasLiveRecordUpdates())->toBeTrue();

    FilamentRealtime::subscribe()->modelsInNamespace('Filament\\Tests\\Fixtures\\Models')->exceptModels([Post::class]);
    expect($page->hasLiveRecordUpdates())->toBeTrue();

    FilamentRealtime::subscribe(Post::class)->onlyEvents([]);
    expect($page->hasLiveRecordUpdates())->toBeFalse();

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated])->exceptEvents([ModelEvent::Updated]);
    expect($page->hasLiveRecordUpdates())->toBeFalse();

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    expect($page->hasLiveRecordUpdates())->toBeTrue();

    Filament::getCurrentOrDefaultPanel()->broadcasting(false);
    expect($page->hasLiveRecordUpdates())->toBeFalse();

    Filament::getCurrentOrDefaultPanel()->broadcasting();
    config(['filament.broadcasting.echo' => null]);
    expect($page->hasLiveRecordUpdates())->toBeFalse();
});

it('renders the current record while preserving edit drafts and refreshing view forms', function (string $pageClass, string $browserTitle): void {
    $post = Post::factory()->create(['title' => 'Original title']);
    FilamentRealtime::subscribe(Post::class);

    FilamentView::registerRenderHook(PanelsRenderHook::PAGE_END, static fn (): string => 'Footer: ' . $post->fresh()->title);

    $page = livewire($pageClass, ['record' => $post->getKey()])
        ->fillForm(['title' => 'Unsaved draft'])
        ->assertSee('Footer: Original title');

    $post->update(['title' => 'Updated elsewhere']);

    $page->call('refreshRecordPage')
        ->assertSchemaStateSet(['title' => $pageClass === ViewPost::class ? 'Updated elsewhere' : 'Unsaved draft'])
        ->assertSee('Footer: Updated elsewhere')
        ->assertReturned($browserTitle);
})->with([
    'edit' => [EditPost::class, 'Edit Updated elsewhere - Laravel'],
    'view' => [ViewPost::class, 'View Updated elsewhere - Laravel'],
]);

it('authorizes only the mounted record channel for the requested socket', function (): void {
    $post = Post::factory()->create();
    $otherPost = Post::factory()->create();
    FilamentRealtime::subscribe(Post::class);

    $authorization = [
        'auth' => 'record-title-test-key:' . hash_hmac('sha256', '123.456:private-' . RecordChangedBroadcast::getChannel($post), 'record-title-test-secret'),
    ];

    livewire(EditPost::class, ['record' => $post->getKey()])
        ->call('authorizeRecordUpdates', '123.456')
        ->assertReturned($authorization);

    expect($authorization['auth'])->not->toBe('record-title-test-key:' . hash_hmac('sha256', '123.456:private-' . RecordChangedBroadcast::getChannel($otherPost), 'record-title-test-secret'));
});

it('rechecks page authorization before refreshing or authorizing a subscription', function (string $method, array $parameters): void {
    $ticket = Ticket::factory()->create();
    FilamentRealtime::subscribe(Ticket::class);

    $page = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::update', static fn (): bool => false);

    $page->call($method, ...$parameters)->assertForbidden();
})->with([
    ['refreshRecordPage', []],
    ['authorizeRecordUpdates', ['123.456']],
]);

it('preserves a hard-delete redirect when a background request is batched with the action', function (string $method, array $parameters, bool $refreshFirst, bool $redirectInCallback): void {
    $ticket = Ticket::factory()->create();
    $ticket->delete();
    FilamentRealtime::subscribe(Ticket::class);

    if ($redirectInCallback) {
        ForceDeleteAction::configureUsing(static fn (ForceDeleteAction $action) => $action
            ->successRedirectUrl('')
            ->action(static function (Ticket $record, EditTicket $livewire): void {
                $record->forceDelete();
                $livewire->redirect($livewire->getResourceUrl());
            }));
    }

    $page = livewire(EditTicket::class, ['record' => $ticket->getKey()])
        ->call('mountAction', 'forceDelete');

    $calls = [
        ['method' => 'callMountedAction', 'params' => [], 'path' => ''],
        ['method' => $method, 'params' => $parameters, 'path' => ''],
    ];

    $page->update(calls: $refreshFirst ? array_reverse($calls) : $calls)
        ->assertSuccessful()->assertRedirect();

    expect($ticket->fresh())->toBeNull();
})->with([
    'refresh after deletion' => ['refreshRecordPage', [], false, false],
    'authorization after deletion' => ['authorizeRecordUpdates', ['123.456'], false, false],
    'refresh before deletion' => ['refreshRecordPage', [], true, false],
    'refresh before callback redirect' => ['refreshRecordPage', [], true, true],
]);

it('rejects updates after a record leaves its mounted parent scope', function (string $method, array $parameters): void {
    $parentRecord = User::factory()->create();
    $otherParentRecord = User::factory()->create();
    $post = Post::factory()->create(['author_id' => $parentRecord->getKey()]);
    FilamentRealtime::subscribe(Post::class);

    $page = livewire(EditUserPost::class, [
        'parentRecord' => $parentRecord,
        'record' => $post->getKey(),
    ]);

    $post->update(['author_id' => $otherParentRecord->getKey()]);

    $page->call($method, ...$parameters)->assertNotFound();
})->with([
    ['refreshRecordPage', []],
    ['authorizeRecordUpdates', ['123.456']],
]);

it('refreshes untouched values from a browser subscription without replacing drafts, newer input, focus or open actions', function (string $operation): void {
    Artisan::call('filament:assets');

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    $post = Post::factory()->create(['title' => 'Original record title', 'rating' => 2]);
    $incomingAuthor = User::factory()->create();
    $draftAuthor = User::factory()->create();

    FilamentView::registerRenderHook(PanelsRenderHook::PAGE_END, static fn (): HtmlString => new HtmlString('<p data-testid="record-footer">Footer: ' . e($post->fresh()->title) . '</p>'));

    // Supply the public Echo integration here; the authorization request still
    // uses the real page and Laravel's Pusher broadcaster.
    FilamentView::registerRenderHook(PanelsRenderHook::SCRIPTS_AFTER, static fn (): HtmlString => new HtmlString(<<<'HTML'
        <script>
            window.EchoFactory = class {
                constructor(configuration) {
                    this.configuration = configuration
                }

                private(channelName) {
                    const transport = window.recordTitleTestTransport = { isSubscribed: false }
                    const channel = {
                        listen(event, callback) {
                            if (event === '.filament.record-changed') transport.notify = callback
                            return channel
                        },
                        subscribed: (callback) => {
                            this.configuration.channelAuthorization.customHandler({ socketId: '123.456', channelName }, (error, authorization) => {
                                if (error || !authorization?.auth) return
                                transport.isSubscribed = true
                                callback()
                            })
                            return channel
                        },
                    }
                    return channel
                }

                disconnect() {}
                leave() {}
            }
        </script>
        HTML));

    $titleInputSelector = 'input[id="form.title"]';

    $page = visit(PostResource::getUrl($operation, ['record' => $post]))
        ->assertScript('window.recordTitleTestTransport?.isSubscribed', true)
        ->assertSeeIn('h1:visible', 'Original record title');

    if ($operation === 'edit') {
        $hasUnsavedChanges = <<<'JS'
            (() => {
                const event = new Event('beforeunload', { cancelable: true })
                window.dispatchEvent(event)
                return event.defaultPrevented
            })()
            JS;

        $page->assertScript($hasUnsavedChanges, false)
            ->fill($titleInputSelector, 'My unsaved draft')
            ->assertScript($hasUnsavedChanges, true);

        $post->update(['rating' => 5]);
        $page->script('window.recordTitleTestTransport.notify([]); undefined');
        $page->assertValue('input[id="form.rating"]', '5')
            ->assertScript($hasUnsavedChanges, true)
            ->fill($titleInputSelector, 'Original record title')
            ->assertScript($hasUnsavedChanges, false)
            ->fill($titleInputSelector, 'My unsaved draft')
            ->assertScript($hasUnsavedChanges, true);
    }

    $post->update(['title' => 'Changed in another session', 'rating' => 8]);
    $page->script('window.recordTitleTestTransport.notify(); undefined');

    $page->assertSeeIn('h1:visible', 'Changed in another session')
        ->assertTitleContains('Changed in another session')
        ->assertSeeIn('nav[aria-label]', 'Changed in another session')
        ->assertSeeIn('[data-testid="record-footer"]', 'Footer: Changed in another session');

    if ($operation === 'edit') {
        $page->assertValue($titleInputSelector, 'My unsaved draft')
            ->assertValue('input[id="form.rating"]', '8')
            ->assertPresent("{$titleInputSelector}:focus");
    } else {
        $page->assertValue($titleInputSelector, 'Changed in another session');
    }

    $page->assertNoAccessibilityIssues();
    $darkPage = visit(PostResource::getUrl($operation, ['record' => $post]))
        ->inDarkMode()
        ->assertScript('window.recordTitleTestTransport?.isSubscribed', true)
        ->assertNoAccessibilityIssues();

    if ($operation !== 'edit') {
        return;
    }

    // Hold a real refresh response while the user starts editing a field
    // that was untouched when the request was sent.
    $page->script(<<<'JS'
        window.originalRecordFetch = window.fetch
        window.holdRecordRefresh = true
        window.fetch = async (...requestArguments) => {
            const response = await window.originalRecordFetch(...requestArguments)
            const payload = requestArguments[1]?.body ? JSON.parse(requestArguments[1].body) : null
            if (window.holdRecordRefresh && payload?.components?.some(component => component.calls.some(call => call.method === 'refreshRecordPage'))) {
                window.holdRecordRefresh = false
                window.recordRefreshHeld = true
                await new Promise(resolve => window.releaseRecordRefresh = resolve)
            }
            return response
        }
        undefined
        JS);

    $post->update(['title' => 'Changed during typing', 'rating' => 6]);
    $page->script('window.recordTitleTestTransport.notify([]); undefined');
    $page->assertScript('window.recordRefreshHeld', true)
        ->fill('input[id="form.rating"]', '4');
    $page->script('window.releaseRecordRefresh(); undefined');

    $page->assertTitleContains('Changed during typing')
        ->assertValue('input[id="form.rating"]', '4')
        ->assertValue($titleInputSelector, 'My unsaved draft')
        ->assertPresent('input[id="form.rating"]:focus');

    expect($post->fresh()->rating)->toBe(6);

    $post->update(['author_id' => $incomingAuthor->getKey()]);
    $page->click('button[aria-label="Actions"]');
    $page->script(<<<'JS'
        window.recordRefreshHeld = false
        window.holdRecordRefresh = true
        document.querySelector('button[role="menuitem"][type="button"]').addEventListener('click', () => window.recordTitleTestTransport.notify([]), { once: true })
        undefined
        JS);
    $page->click('button[role="menuitem"][type="button"]')
        ->assertScript('window.recordRefreshHeld', true)
        ->select('select[id="form.author_id"]', (string) $draftAuthor->getKey());
    $page->script('window.releaseRecordRefresh(); undefined');
    $page->assertVisible('[role="alertdialog"] h2')
        ->assertValue('select[id="form.author_id"]', (string) $draftAuthor->getKey());

    $post->update(['title' => 'Changed while an action is open']);
    $page->script('window.recordTitleTestTransport.notify(); undefined');

    $page->assertTitleContains('Changed while an action is open')
        ->assertSeeIn('h1:visible', 'Changed while an action is open')
        ->assertSeeIn('[data-testid="record-footer"]', 'Footer: Changed while an action is open')
        ->assertVisible('[role="alertdialog"] h2')
        ->assertValue($titleInputSelector, 'My unsaved draft')
        ->assertValue('input[id="form.rating"]', '4')
        ->assertNoAccessibilityIssues();

    $darkPage->click('button[aria-label="Actions"]')
        ->click('button[role="menuitem"][type="button"]')
        ->assertVisible('[role="alertdialog"] h2')
        ->assertNoAccessibilityIssues();

    $page
        ->click('[role="alertdialog"] button[type="submit"]')
        ->assertPathIs('/posts');

    expect($post->fresh()->trashed())->toBeTrue();
})->with(['edit', 'view']);

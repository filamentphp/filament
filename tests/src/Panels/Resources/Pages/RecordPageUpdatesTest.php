<?php

use Filament\Actions\ForceDeleteAction;
use Filament\Facades\Filament;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChangedBroadcast;
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

    Event::fake([ModelChangedBroadcast::class]);
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

it('renders the current record without refilling the form', function (string $pageClass, string $browserTitle): void {
    $post = Post::factory()->create(['title' => 'Original title']);
    FilamentRealtime::subscribe(Post::class);

    FilamentView::registerRenderHook(PanelsRenderHook::PAGE_END, fn (): string => 'Footer: ' . $post->fresh()->title);

    $page = livewire($pageClass, ['record' => $post->getKey()])
        ->fillForm(['title' => 'Unsaved draft'])
        ->assertSee('Footer: Original title');

    $post->update(['title' => 'Updated elsewhere']);

    $page->call('refreshRecordPage')
        ->assertSchemaStateSet(['title' => 'Unsaved draft'])
        ->assertSee('Footer: Updated elsewhere')
        ->assertReturned($browserTitle);
})->with([
    'edit' => [EditPost::class, 'Edit Updated elsewhere - Laravel'],
    'view' => [ViewPost::class, 'View Updated elsewhere - Laravel'],
]);

it('strips HTML from the browser title without using the page heading', function (): void {
    $post = Post::factory()->create(['title' => '<script>alert(1)</script> & title']);

    $page = new class extends EditPost
    {
        public function getTitle(): string
        {
            return 'Browser: ' . $this->getRecord()->title;
        }

        public function getHeading(): string
        {
            return 'Distinct heading';
        }
    };

    $page->record = $post;
    expect($page->getBrowserTitle())->toBe('Browser: alert(1) & title - Laravel');
});

it('authorizes only the mounted record channel for the requested socket', function (): void {
    $post = Post::factory()->create();
    $otherPost = Post::factory()->create();
    FilamentRealtime::subscribe(Post::class);

    $authorization = [
        'auth' => 'record-title-test-key:' . hash_hmac('sha256', '123.456:private-' . ModelChangedBroadcast::getChannel($post), 'record-title-test-secret'),
    ];

    livewire(EditPost::class, ['record' => $post->getKey()])
        ->call('authorizeRecordUpdates', '123.456')
        ->assertReturned($authorization);

    expect($authorization['auth'])->not->toBe('record-title-test-key:' . hash_hmac('sha256', '123.456:private-' . ModelChangedBroadcast::getChannel($otherPost), 'record-title-test-secret'));
});

it('rechecks page authorization before refreshing or authorizing a subscription', function (string $method, array $parameters): void {
    $ticket = Ticket::factory()->create();
    FilamentRealtime::subscribe(Ticket::class);

    $page = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

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

it('refreshes the page from a browser subscription without replacing drafts, focus or open actions', function (string $operation): void {
    Artisan::call('filament:assets');

    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    $post = Post::factory()->create(['title' => 'Original record title']);

    FilamentView::registerRenderHook(PanelsRenderHook::PAGE_END, fn (): HtmlString => new HtmlString('<p data-testid="record-footer">Footer: ' . e($post->fresh()->title) . '</p>'));

    // Supply the public Echo integration here; the authorization request still
    // uses the real page and Laravel's Pusher broadcaster.
    FilamentView::registerRenderHook(PanelsRenderHook::SCRIPTS_AFTER, fn (): HtmlString => new HtmlString(<<<'HTML'
        <script>
            window.EchoFactory = class {
                constructor(configuration) {
                    this.configuration = configuration
                }

                private(channelName) {
                    const transport = window.recordTitleTestTransport = { isSubscribed: false }
                    const channel = {
                        listen(event, callback) {
                            transport.notify = callback
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
            }
        </script>
        HTML));

    $page = visit(PostResource::getUrl($operation, ['record' => $post]))
        ->assertScript('window.recordTitleTestTransport?.isSubscribed', true)
        ->assertScript('document.querySelector("h1")?.textContent.includes("Original record title")', true);

    if ($operation === 'edit') {
        $page->fill('input[wire\\:model="data.title"]', 'My unsaved draft')
            ->click('input[wire\\:model="data.title"]');
    }

    $post->update(['title' => 'Changed in another session']);
    $page->script('window.recordTitleTestTransport.notify(); undefined');

    $page->assertScript('document.querySelector("h1")?.textContent.includes("Changed in another session")', true)
        ->assertScript('document.title.includes("Changed in another session")', true)
        ->assertSeeIn('nav[aria-label]', 'Changed in another session')
        ->assertSeeIn('[data-testid="record-footer"]', 'Footer: Changed in another session');

    if ($operation === 'edit') {
        $page->assertValue('input[wire\\:model="data.title"]', 'My unsaved draft')
            ->assertPresent('input[wire\\:model="data.title"]:focus');
    } else {
        $page->assertValue('input[wire\\:model="data.title"]', 'Original record title');
    }

    $page->assertNoAccessibilityIssues();
    visit(PostResource::getUrl($operation, ['record' => $post]))->inDarkMode()->assertNoAccessibilityIssues();

    if ($operation !== 'edit') {
        return;
    }

    $post->update(['title' => 'Changed while opening an action']);

    // Delay the title response across action mounting to catch stale snapshots
    // that would leave the modal open but discard its executable action state.
    $page->script(<<<'JS'
        window.recordTitleRace = { isHeld: false }
        const originalFetch = window.fetch.bind(window)
        window.fetch = async (...arguments_) => {
            const calls = JSON.parse(arguments_[1]?.body ?? '{}').components?.flatMap(component => component.calls) ?? []
            if (calls.some(call => call.method === 'mountAction')) {
                window.recordTitleTestTransport.notify()
            }
            const response = await originalFetch(...arguments_)
            if (calls.some(call => call.method === 'refreshRecordPage')) {
                window.recordTitleRace.isHeld = true
                await new Promise(resolve => { window.recordTitleRace.release = resolve })
                window.fetch = originalFetch
            }
            return response
        }
        undefined
        JS);

    $page->click('button[aria-label="Actions"]')
        ->click('button[role="menuitem"][type="button"]')
        ->assertScript('window.recordTitleRace.isHeld', true)
        ->assertVisible('[role="alertdialog"] h2');

    $page->script('window.recordTitleRace.release(); undefined');

    $page->assertScript('document.title.includes("Changed while opening an action")', true)
        ->assertScript('document.querySelector("h1")?.textContent.includes("Changed while opening an action")', true)
        ->assertSeeIn('[data-testid="record-footer"]', 'Footer: Changed while opening an action')
        ->assertVisible('[role="alertdialog"] h2')
        ->assertValue('input[wire\\:model="data.title"]', 'My unsaved draft')
        ->click('[role="alertdialog"] button[type="submit"]')
        ->assertPathIs('/posts');

    expect($post->fresh()->trashed())->toBeTrue();
})->with(['edit', 'view']);

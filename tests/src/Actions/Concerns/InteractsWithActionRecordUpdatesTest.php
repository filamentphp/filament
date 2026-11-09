<?php

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\ModelEvent;
use Filament\Support\Events\ModelChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
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
            'key' => 'view-test-key',
            'secret' => 'view-test-secret',
            'app_id' => 'view-test',
            'options' => ['cluster' => 'mt1'],
        ],
    ]);

    Event::fake([ModelChangedBroadcast::class]);
});

it('enables modal updates only for an effective subscription and configured broadcasting', function (): void {
    expect(Action::make('custom')->recordUpdateReceived(static function (): void {})->shouldReceiveRecordUpdates())->toBeFalse();
    expect(ViewAction::make()->receiveRecordUpdates(false)->shouldReceiveRecordUpdates())->toBeFalse();

    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $livewire = $component->instance();
    $action = $livewire->getMountedAction();

    expect($livewire->getActionRecordUpdates($action))->toBeNull();

    FilamentRealtime::subscribe()->allModels();
    expect($livewire->getActionRecordUpdates($action))->not->toBeNull();

    FilamentRealtime::subscribe(Department::class)->onlyEvents([]);
    expect($livewire->getActionRecordUpdates($action))->toBeNull();

    FilamentRealtime::subscribe(Department::class)->onlyEvents([ModelEvent::Updated]);
    Filament::setCurrentPanel(Filament::getDefaultPanel());
    Filament::getCurrentOrDefaultPanel()->broadcasting(false);
    expect($livewire->getActionRecordUpdates($action))->toBeNull();

    Filament::getCurrentOrDefaultPanel()->broadcasting();
    Filament::setCurrentPanel(null);
    expect($livewire->getActionRecordUpdates($action))->not->toBeNull();

    config(['filament.broadcasting.echo' => null]);
    expect($livewire->getActionRecordUpdates($action))->toBeNull();
});

it('requires opt-in and binds callbacks to their own action schema while preserving drafts and requested rendering', function (bool $isEnabled, bool $isDisabled, string $render = 'target'): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    ViewAction::configureUsing(static fn (ViewAction $action) => $action->extraModalFooterActions([
        Action::make('review')
            ->receiveRecordUpdates(static fn (): bool => $isEnabled)
            ->disabledSchema($isDisabled)
            ->schema([TextInput::make('name'), TextInput::make('latest')])
            ->fillForm(static fn (Department $record): array => ['name' => $record->name, 'latest' => $record->name])
            ->recordUpdateReceived(static function (Action $action, Department $record, $livewire, Schema $schema, Schema $form) use ($render): void {
                $schema->fillPartially(['latest' => $record->name], ['latest']);
                $livewire->dispatch('record-update-received', name: $record->name, action: $action->getName(), statePath: $schema->getStatePath(), sameSchema: $schema === $form);

                if ($render === 'child') {
                    app(PartialsComponentHook::class)->renderPartial($livewire, fn (): array => [
                        'action-modals.2' => $livewire->getMountedAction()->renderModal(),
                    ]);
                } elseif ($render === 'full') {
                    app(PartialsComponentHook::class)->forceRender($livewire);
                }
            })
            ->extraModalFooterActions([
                Action::make('addNote')
                    ->modalHeading(static fn (Department $record): string => "Review {$record->name}")
                    ->schema([TextInput::make('note')]),
            ]),
    ]));

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->mountAction('review')
        ->fillForm(['name' => 'Retained draft']);
    $subscription = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction());

    if (! $isEnabled) {
        expect($subscription)->toBeNull();
        $component->call('refreshAction', 'unsubscribed')->assertReturned(false)
            ->assertSchemaStateSet(['name' => 'Retained draft'])
            ->assertNotDispatched('record-update-received');

        return;
    }

    $component->mountAction('addNote')->fillForm(['note' => 'Nested draft']);
    $department->update(['name' => 'Changed elsewhere']);

    $component->call('refreshAction', $subscription['key'])->assertReturned(true)
        ->assertSet('mountedActions.1.data.name', 'Retained draft')
        ->assertSet('mountedActions.1.data.latest', 'Changed elsewhere')
        ->assertSchemaStateSet(['note' => 'Nested draft'])
        ->assertActionMounted('addNote')
        ->assertDispatched('record-update-received', name: 'Changed elsewhere', action: 'review', statePath: 'mountedActions.1.data', sameSchema: true);

    if ($render === 'child') {
        expect($component->effects['partials']['action-modals.2'])->toContain('Review Changed elsewhere');
    } elseif ($render === 'full') {
        $component->assertSee('Review Changed elsewhere');
        expect($component->effects['partials'] ?? [])->toBe([]);
    } else {
        expect(array_keys($component->effects['partials']))->toBe(['action-modals.1']);
    }
})->with([[false, false], [true, false], [true, true], [true, false, 'child'], [true, false, 'full']]);

it('refills disabled view fields through their data mapping without replaying fill hooks', function (): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    ViewAction::configureUsing(static fn (ViewAction $action) => $action
        ->mutateRecordDataUsing(static fn (array $data): array => [...$data, 'name' => strtoupper($data['name'])])
        ->beforeFormFilled(static fn ($livewire) => $livewire->dispatch('before-view-filled'))
        ->afterFormFilled(static fn ($livewire) => $livewire->dispatch('after-view-filled')));

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertSchemaStateSet(['name' => 'ORIGINAL']);
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $department->update(['name' => 'Updated elsewhere']);

    $component->call('refreshAction', $key)
        ->assertReturned(true)
        ->assertActionMounted(TestAction::make(ViewAction::class)->table($department))
        ->assertSchemaStateSet(['name' => 'UPDATED ELSEWHERE'])
        ->assertNotDispatched('before-view-filled')
        ->assertNotDispatched('after-view-filled');
});

it('signs only the mounted view record channel and rejects old mounts', function (): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $component->call('authorizeActionRecordUpdates', $key, '123.456')
        ->assertReturned(['auth' => 'view-test-key:' . hash_hmac('sha256', '123.456:private-' . ModelChangedBroadcast::getChannel($department), 'view-test-secret')])
        ->unmountAction()
        ->mountAction(TestAction::make(ViewAction::class)->table($department));

    $department->update(['name' => 'Newer value']);

    $component->call('refreshAction', $key)->assertReturned(false)
        ->call('authorizeActionRecordUpdates', $key, '123.456')->assertReturned(null)
        ->assertSchemaStateSet(['name' => 'Original']);

    $currentKey = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];
    $replacement = Department::factory()->hasAttached($ticket)->create(['name' => 'Different record']);

    $component->set('mountedActions.0.context.recordKey', (string) $replacement->getKey())
        ->call('refreshAction', $currentKey)->assertReturned(false)
        ->call('authorizeActionRecordUpdates', $currentKey, '123.456')->assertReturned(null)
        ->assertSchemaStateSet(['name' => 'Original']);
});

it('rechecks action authorization before refreshing or signing', function (string $method, array $parameters): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    app()->bind(DepartmentPolicy::class . '::view', static fn (): bool => false);

    $component->call($method, $key, ...$parameters)->assertForbidden();
})->with([
    ['refreshAction', []],
    ['authorizeActionRecordUpdates', ['123.456']],
]);

it('rejects deleted or out-of-scope view records', function (string $method, array $parameters, bool $isDeleted): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    if ($isDeleted) {
        $department->forceDelete();
    } else {
        $ticket->departments()->detach($department);
    }

    $component->call($method, $key, ...$parameters)->assertNotFound();
})->with([
    ['refreshAction', []],
    ['authorizeActionRecordUpdates', ['123.456']],
])->with([true, false]);

it('refreshes a parent view without changing an open child edit draft', function (): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    ViewAction::configureUsing(static fn (ViewAction $action) => $action->extraModalFooterActions([
        EditAction::make('editDetails')->schema([TextInput::make('name')]),
    ]));

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $component->mountAction('editDetails')->fillForm(['name' => 'Child draft']);
    $department->update(['name' => 'Updated parent']);

    $component->call('refreshAction', $key)->assertReturned(true)
        ->assertSet('mountedActions.0.data.name', 'Updated parent')
        ->assertSchemaStateSet(['name' => 'Child draft'])
        ->assertActionMounted('editDetails')
        ->callMountedAction();

    expect($department->fresh()->name)->toBe('Child draft');
});

it('refreshes a child view without replacing its editing parent', function (): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    EditAction::configureUsing(static fn (EditAction $action) => $action->extraModalFooterActions([
        ViewAction::make('viewDetails')->schema([TextInput::make('name')]),
    ]));

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->fillForm(['name' => 'Parent draft'])
        ->mountAction('viewDetails');
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $department->update(['name' => 'Updated child']);

    $component->call('refreshAction', $key)->assertReturned(true)
        ->assertSet('mountedActions.0.data.name', 'Parent draft')
        ->assertSchemaStateSet(['name' => 'Updated child'])
        ->unmountAction()
        ->assertActionMounted(TestAction::make(EditAction::class)->table($department))
        ->assertSchemaStateSet(['name' => 'Parent draft']);
});

it('does not replay a custom mount callback or overwrite an enabled schema', function (bool $hasCustomMount): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    ViewAction::configureUsing(static function (ViewAction $action) use ($hasCustomMount): void {
        if ($hasCustomMount) {
            $action->mountUsing(static function ($schema, $livewire): void {
                $schema->fill(['name' => 'Custom mounted value']);
                $livewire->dispatch('view-mounted');
            });
        } else {
            $action->disabledSchema(false);
        }
    });

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department));
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $department->update(['name' => 'Updated elsewhere']);

    $component->call('refreshAction', $key)
        ->assertSchemaStateSet(['name' => $hasCustomMount ? 'Custom mounted value' : 'Original'])
        ->assertNotDispatched('view-mounted');
})->with([true, false]);

it('uses a configured `fillForm()` mapping on every refill', function (): void {
    FilamentRealtime::subscribe(Department::class);
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

    ViewAction::configureUsing(static fn (ViewAction $action) => $action->fillForm(static fn (Department $record): array => ['name' => "Mapped {$record->name}"]));

    $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertSchemaStateSet(['name' => 'Mapped Original']);
    $key = $component->instance()->getActionRecordUpdates($component->instance()->getMountedAction())['key'];

    $department->update(['name' => 'Replacement']);

    $component->call('refreshAction', $key)->assertSchemaStateSet(['name' => 'Mapped Replacement']);
});

it('refreshes modal fields and entries on invalidation and resubscription while retaining nested drafts', function (): void {
    Artisan::call('filament:assets');
    FilamentRealtime::subscribe(Post::class)->onlyEvents([ModelEvent::Updated]);
    $post = Post::factory()->create(['title' => 'Original record', 'content' => 'Original content']);

    ViewAction::configureUsing(static fn (ViewAction $action) => $action
        ->modal()
        ->extraAttributes(['data-testid' => 'open-live-view'])
        ->extraModalWindowAttributes(['data-testid' => 'live-view-modal'])
        ->modalCancelAction(static fn (Action $action) => $action->extraAttributes(['data-testid' => 'close-live-view']))
        ->schema([
            TextInput::make('title')->id('live-view-title'),
            TextEntry::make('content')->extraAttributes(['data-testid' => 'live-view-content']),
        ])
        ->extraModalFooterActions([
            Action::make('addNote')
                ->extraAttributes(['data-testid' => 'add-view-note'])
                ->receiveRecordUpdates()
                ->schema([
                    TextInput::make('note')->id('live-view-note'),
                    TextInput::make('latest')->disabled()->id('live-note-latest'),
                ])
                ->fillForm(static fn (Post $record): array => ['latest' => $record->title])
                ->recordUpdateReceived(static fn (Post $record, Schema $schema) => $schema->fillPartially(['latest' => $record->title], ['latest']))
                ->modalSubmitAction(static fn (Action $action) => $action->extraAttributes(['data-testid' => 'submit-view-note']))
                ->action(static fn ($livewire) => $livewire->dispatch('view-note-submitted')),
        ]));

    // Supply Echo's public transport contract; signing still uses the mounted
    // action/page and the installed Laravel Pusher broadcaster.
    FilamentView::registerRenderHook(PanelsRenderHook::SCRIPTS_AFTER, static fn (): HtmlString => new HtmlString(<<<'HTML'
        <script>
            window.viewActionTestConnections = []
            window.EchoFactory = class {
                constructor(configuration) {
                    this.configuration = configuration
                    this.transport = { isSubscribed: false, isDisconnected: false }
                    window.viewActionTestConnections.push(this.transport)
                }
                private(channelName) {
                    const transport = this.transport
                    const channel = {
                        listen(event, callback) {
                            if (event === '.filament.model-changed') transport.notify = callback
                            return channel
                        },
                        subscribed: (callback) => {
                            transport.reconnect = () => this.configuration.channelAuthorization.customHandler({ socketId: '123.456', channelName }, (error, authorization) => {
                                if (error || !authorization?.auth) return
                                transport.isSubscribed = true
                                callback()
                            })
                            transport.reconnect()
                            return channel
                        },
                    }
                    return channel
                }
                disconnect() { this.transport.isDisconnected = true }
            }
        </script>
        HTML));

    $page = visit(PostResource::getUrl('edit', ['record' => $post]));

    $page->assertScript('window.viewActionTestConnections.filter(connection => connection.isSubscribed && !connection.isDisconnected).length', 1)
        ->fill('input[id="form.title"]', 'Unsaved page draft')
        ->click('[data-testid="open-live-view"]')
        ->assertVisible('[data-testid="live-view-modal"]')
        ->assertValue('#live-view-title', 'Original record')
        ->assertDisabled('#live-view-title')
        ->assertScript('window.viewActionTestConnections.filter(connection => connection.isSubscribed && !connection.isDisconnected).length', 2);

    $page->script('window.originalViewInput = document.getElementById("live-view-title"); undefined');
    $post->update(['title' => 'Updated record', 'content' => 'Updated content']);
    $page->script('window.viewActionTestConnections.filter(connection => !connection.isDisconnected).forEach(connection => connection.notify()); undefined');

    $page->assertValue('#live-view-title', 'Updated record')
        ->assertSeeIn('[data-testid="live-view-content"]', 'Updated content')
        ->assertValue('input[id="form.title"]', 'Unsaved page draft')
        ->assertScript('window.originalViewInput === document.getElementById("live-view-title")', true)
        ->assertNoAccessibilityIssues();

    $page->click('[data-testid="add-view-note"]')
        ->fill('#live-view-note', 'Nested unsaved note')
        ->assertValue('#live-note-latest', 'Updated record')
        ->assertScript('window.viewActionTestConnections.filter(connection => connection.isSubscribed && !connection.isDisconnected).length', 3);

    $post->update(['title' => 'Changed while note is open']);
    $page->script('window.viewActionTestConnections.filter(connection => !connection.isDisconnected).forEach(connection => connection.reconnect()); undefined');

    $page->assertPresent('#live-view-title')
        ->assertValue('#live-view-title', 'Changed while note is open')
        ->assertValue('#live-view-note', 'Nested unsaved note')
        ->assertValue('#live-note-latest', 'Changed while note is open')
        ->assertPresent('#live-view-note:focus')
        ->assertValue('input[id="form.title"]', 'Unsaved page draft')
        ->click('[data-testid="submit-view-note"]')
        ->assertNotPresent('#live-view-note')
        ->assertVisible('[data-testid="live-view-modal"]')
        ->click('[data-testid="close-live-view"]')
        ->assertScript('window.viewActionTestConnections.filter(connection => connection.isDisconnected).length', 2)
        ->click('[data-testid="open-live-view"]')
        ->assertValue('#live-view-title', 'Changed while note is open')
        ->assertScript('window.viewActionTestConnections.filter(connection => connection.isSubscribed && !connection.isDisconnected).length', 2);

    $page->inDarkMode()
        ->click('[data-testid="open-live-view"]')
        ->assertVisible('[data-testid="live-view-modal"]')
        ->assertNoAccessibilityIssues();
});

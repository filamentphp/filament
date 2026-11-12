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
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\ModalBrowserTest;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('event listeners', function (): void {
    beforeEach(function (): void {
        config(['filament.broadcasting.echo' => ['broadcaster' => 'null']]);
        WebSocketActions::$isAuthorized = true;
        WebSocketActions::$listener = 'orders,.OrderShipped';
    });

    it('listens without a record and injects the payload and target schema beneath an open child', function (string $listener, string $channelType): void {
        WebSocketActions::$listener = $listener;

        $component = livewire(WebSocketActions::class)
            ->mountAction('review')
            ->fillForm(['note' => 'Parent draft']);
        $action = $component->instance()->getMountedAction();
        $configuration = $component->instance()->getActionEventListeners($action)[0];

        expect($action->getRecord())->toBeNull()
            ->and($configuration['channel'])->toBe('orders')
            ->and($configuration['channelType'])->toBe($channelType)
            ->and($configuration['eventName'])->toBe('.OrderShipped')
            ->and($configuration)->not->toHaveKey('authorizationMethod');

        $component->mountAction('child')->fillForm(['note' => 'Child draft'])
            ->call('callActionWebSocketListener', ...[...$configuration['parameters'], ['status' => 'Shipped', 'sequence' => 7]])
            ->assertReturned(true)
            ->assertSet('mountedActions.0.data.note', 'Parent draft')
            ->assertSet('mountedActions.0.data.latest', 'Shipped')
            ->assertSchemaStateSet(['note' => 'Child draft'])
            ->assertActionMounted('child')
            ->assertDispatched('shipment-received', sequence: 7, action: 'review', path: 'mountedActions.0.data', sameSchema: true);

        expect(array_keys($component->effects['partials']))->toBe(['action-modals.0']);
    })->with([
        ['orders,.OrderShipped', 'channel'],
        ['private:orders,.OrderShipped', 'private'],
        ['presence:orders,.OrderShipped', 'join'],
        ['encrypted-private:orders,.OrderShipped', 'encryptedPrivate'],
    ]);

    it('supports callback-free rerenders after updating action arguments or array records without refilling drafts', function (): void {
        $record = new ArrayObject(['__key' => 'shipment-123', 'status' => 'Original']);

        Action::configureUsing(static function (Action $action) use ($record): void {
            if ($action->getName() !== 'review') {
                return;
            }

            $action
                ->record(static fn (): array => $record->getArrayCopy())
                ->livewireListeners([
                    'arguments-changed' => static function ($livewire): void {
                        $livewire->mergeMountedActionArguments(['version' => 2]);
                    },
                ]);
        });

        $component = livewire(WebSocketActions::class)
            ->mountAction('review', ['version' => 1])
            ->fillForm(['note' => 'Retained draft']);
        $configuration = collect($component->instance()->getActionEventListeners($component->instance()->getMountedAction()))->firstWhere('eventName', '.StatusChanged');

        $component->call('callActionWebSocketListener', ...[...$configuration['parameters'], ['status' => 'Ignored']])
            ->assertReturned(true)
            ->assertSchemaStateSet(['note' => 'Retained draft', 'latest' => null])
            ->assertNotDispatched('shipment-received');

        expect($component->effects['partials']['action-modals.0'])->toContain('Live shipping review');

        $component->call('callActionLivewireListener', $configuration['parameters'][0], 'arguments-changed')
            ->assertReturned(true)
            ->assertSet('mountedActions.0.arguments.version', 2)
            ->call('callActionWebSocketListener', ...$configuration['parameters'])
            ->assertReturned(true)
            ->assertSchemaStateSet(['note' => 'Retained draft', 'latest' => null]);

        $record['status'] = 'Updated';

        $component->call('callActionWebSocketListener', ...$configuration['parameters'])
            ->assertReturned(true)
            ->assertSchemaStateSet(['note' => 'Retained draft', 'latest' => null]);
    });

    it('rejects unregistered events and resolves listeners from the current mounted action', function (): void {
        $component = livewire(WebSocketActions::class)->mountAction('review');
        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];
        $actionNestingIndex = $configuration['parameters'][0];

        $component->call('callActionWebSocketListener', $actionNestingIndex, 'private:secrets,.Leaked', [])->assertNotFound();

        $component = livewire(WebSocketActions::class)->mountAction('review');
        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];

        $component->call('callActionWebSocketListener', -1, $configuration['parameters'][1])->assertReturned(false)
            ->call('callActionWebSocketListener', 1, $configuration['parameters'][1])->assertReturned(false)
            ->assertSchemaStateSet(['latest' => null])
            ->unmountAction()
            ->call('callActionWebSocketListener', ...$configuration['parameters'])
            ->assertReturned(false)
            ->assertNotDispatched('shipment-received')
            ->mountAction('review')
            ->call('callActionWebSocketListener', ...[...$configuration['parameters'], ['status' => 'Current', 'sequence' => 1]])
            ->assertReturned(true)
            ->assertSchemaStateSet(['latest' => 'Current'])
            ->assertDispatched('shipment-received', sequence: 1);
    });

    it('rechecks target and ancestor authorization for custom events', function (bool $isChildTarget): void {
        $component = livewire(WebSocketActions::class)->mountAction('review');

        if ($isChildTarget) {
            $component->mountAction('child');
        }

        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];
        WebSocketActions::$isAuthorized = false;

        $component->call('callActionWebSocketListener', ...[...$configuration['parameters'], ['status' => 'Unauthorized', 'sequence' => 1]])
            ->assertForbidden();
    })->with([false, true]);

    it('delivers arbitrary events in order without a record, keeps drafts and disconnects on close', function (): void {
        Artisan::call('filament:assets');

        Action::configureUsing(static function (Action $action): void {
            if ($action->getName() !== 'modalFocusRestoration') {
                return;
            }

            $action
                ->schema([
                    TextInput::make('note')->id('shipment-note'),
                    TextInput::make('latest')->disabled()->id('shipment-latest'),
                ])
                ->webSocketListeners([
                    'private:orders,.OrderShipped' => static function (array $event, Action $action, Schema $schema): void {
                        $schema->fillPartially(['latest' => ($action->getRawData()['latest'] ?? '') . $event['status']], ['latest']);
                    },
                ]);
        });

        FilamentView::registerRenderHook(PanelsRenderHook::SCRIPTS_AFTER, static fn (): HtmlString => new HtmlString(<<<'HTML'
        <script>
            window.shipmentConnections = []
            window.EchoFactory = class {
                constructor(configuration) {
                    this.connection = { configuration, disconnected: false }
                    window.shipmentConnections.push(this.connection)
                }
                private(name) {
                    this.connection.channel = name
                    return {
                        listen: (event, callback) => {
                            this.connection.event = event
                            this.connection.receive = callback
                        },
                    }
                }
                disconnect() { this.connection.disconnected = true }
                leave() {}
            }
        </script>
        HTML));

        $page = visit(ModalBrowserTest::getUrl());

        $page->click('[data-testid="basic-trigger"]')
            ->assertVisible('[data-testid="basic-modal"]')
            ->assertScript('window.shipmentConnections.length', 1)
            ->assertScript('window.shipmentConnections[0].channel', 'orders')
            ->assertScript('window.shipmentConnections[0].event', '.OrderShipped')
            ->assertScript('window.shipmentConnections[0].configuration.channelAuthorization === undefined', true)
            ->fill('#shipment-note', 'Keep this draft');

        $page->script('window.shipmentConnections[0].receive({ status: "First" }); window.shipmentConnections[0].receive({ status: "Second" }); undefined');

        $page->assertValue('#shipment-latest', 'FirstSecond')
            ->assertValue('#shipment-note', 'Keep this draft')
            ->assertPresent('#shipment-note:focus')
            ->assertNoAccessibilityIssues()
            ->press('Cancel')
            ->assertNotPresent('#shipment-note')
            ->assertScript('window.shipmentConnections[0].disconnected', true)
            ->click('[data-testid="basic-trigger"]')
            ->assertValue('#shipment-latest', '')
            ->assertScript('window.shipmentConnections.length', 2);

        $page->script('window.shipmentConnections[0].receive({ status: "Stale" }); window.shipmentConnections[1].receive({ status: "Current" }); undefined');

        $page->assertValue('#shipment-latest', 'Current');
    });

    it('listens for Livewire events without Echo and binds callbacks to the parent beneath a child', function (): void {
        config(['filament.broadcasting.echo' => null]);
        Action::configureUsing(static fn (Action $action) => $action->livewireListeners([
            'shipment-updated' => static function (array $event, Schema $schema, Schema $form, $livewire): void {
                $schema->fillPartially(['latest' => $event['status']], ['latest']);
                $livewire->dispatch('livewire-shipment-received', sameSchema: $schema === $form);
            },
        ]));

        $component = livewire(WebSocketActions::class)->mountAction('review')->fillForm(['note' => 'Parent draft']);
        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];

        expect($configuration['eventType'])->toBe('livewire');

        $component->mountAction('child')->fillForm(['note' => 'Child draft'])
            ->call('callActionLivewireListener', ...[...$configuration['parameters'], ['status' => 'Shipped']])
            ->assertReturned(true)
            ->assertSet('mountedActions.0.data.note', 'Parent draft')
            ->assertSet('mountedActions.0.data.latest', 'Shipped')
            ->assertSchemaStateSet(['note' => 'Child draft'])
            ->assertDispatched('livewire-shipment-received', sameSchema: true);

        $component->unmountAction()->unmountAction()->mountAction('review')
            ->call('callActionLivewireListener', ...[...$configuration['parameters'], ['status' => 'Current']])
            ->assertReturned(true)
            ->assertSchemaStateSet(['latest' => 'Current']);

        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];
        WebSocketActions::$isAuthorized = false;
        $component->call('callActionLivewireListener', ...[...$configuration['parameters'], ['status' => 'Unauthorized']])
            ->assertForbidden();
    });

    it('receives global, self and targeted Livewire events only while the modal is mounted', function (): void {
        Artisan::call('filament:assets');
        config(['filament.broadcasting.echo' => null]);

        Action::configureUsing(static function (Action $action): void {
            if ($action->getName() !== 'modalFocusRestoration') {
                return;
            }

            $action->schema([
                TextInput::make('note')->id('livewire-note'),
                TextInput::make('latest')->disabled()->id('livewire-latest'),
            ])->livewireListeners(static fn (): array => [
                'shipment-updated' => static function (array $event, Action $action, Schema $schema): void {
                    $schema->fillPartially(['latest' => ($action->getRawData()['latest'] ?? '') . $event['status']], ['latest']);
                },
                'shipment-progress' => static function (array $event, Action $action, Schema $schema): void {
                    $schema->fillPartially(['latest' => ($action->getRawData()['latest'] ?? '') . $event['status']], ['latest']);
                },
                'shipment-rerender',
            ]);
        });

        $componentName = json_encode(Livewire::new(ModalBrowserTest::class)->getName());
        $page = visit(ModalBrowserTest::getUrl());
        $page->script('Livewire.dispatch("shipment-updated", { status: "Before mount" }); undefined');
        $page->click('[data-testid="basic-trigger"]')->assertValue('#livewire-latest', '')
            ->fill('#livewire-note', 'Keep my draft');

        $page->script('Livewire.dispatch("shipment-updated", { status: "Global" }); undefined');
        $page->assertValue('#livewire-latest', 'Global');

        $page->script('Livewire.getByName(' . $componentName . ')[0].$dispatchSelf("shipment-updated", { status: "Self" }); undefined');
        $page->assertValue('#livewire-latest', 'GlobalSelf');

        $page->script('Livewire.dispatchTo(' . $componentName . ', "shipment-updated", { status: "Targeted" }); undefined');
        $page->assertValue('#livewire-latest', 'GlobalSelfTargeted');

        $page->script('Livewire.dispatch("shipment-updated", { status: "First" }); Livewire.dispatch("shipment-updated", { status: "Second" }); Livewire.dispatch("shipment-progress", { status: "Third" }); undefined');
        $page->assertValue('#livewire-latest', 'GlobalSelfTargetedFirstSecondThird');

        $page->script('for (let index = 1; index <= 51; index++) Livewire.dispatch(index % 2 ? "shipment-updated" : "shipment-progress", { status: `|${index}` }); undefined');
        $expected = 'GlobalSelfTargetedFirstSecondThird' . implode('', array_map(static fn (int $index): string => "|{$index}", range(1, 51)));
        $page->assertValue('#livewire-latest', $expected);

        $page->script('Livewire.dispatch("shipment-rerender"); undefined');
        $page->assertValue('#livewire-latest', $expected)
            ->assertValue('#livewire-note', 'Keep my draft')
            ->assertNoAccessibilityIssues()
            ->press('Cancel')->assertNotPresent('#livewire-note');

        $page->script('Livewire.dispatch("shipment-updated", { status: "Closed" }); undefined');
        $page->click('[data-testid="basic-trigger"]')->assertValue('#livewire-latest', '');
        $page->script('Livewire.dispatch("shipment-updated", { status: "New mount" }); undefined');
        $page->assertValue('#livewire-latest', 'New mount');
    });
});

describe('record updates', function (): void {
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
        expect(Action::make('custom')->afterRecordUpdateReceived(static function (): void {})->shouldReceiveRecordUpdates())->toBeFalse();
        expect(ViewAction::make()->receiveRecordUpdates(false)->shouldReceiveRecordUpdates())->toBeFalse();

        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create();
        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(ViewAction::class)->table($department));
        $livewire = $component->instance();
        $action = $livewire->getMountedAction();

        expect($livewire->getActionEventListeners($action))->toBeEmpty();

        FilamentRealtime::subscribe()->allModels();
        expect($livewire->getActionEventListeners($action))->toHaveCount(1);

        FilamentRealtime::subscribe(Department::class)->onlyEvents([]);
        expect($livewire->getActionEventListeners($action))->toBeEmpty();

        FilamentRealtime::subscribe(Department::class)->onlyEvents([ModelEvent::Updated]);
        Filament::setCurrentPanel(Filament::getDefaultPanel());
        Filament::getCurrentOrDefaultPanel()->broadcasting(false);
        expect($livewire->getActionEventListeners($action))->toBeEmpty();

        Filament::getCurrentOrDefaultPanel()->broadcasting();
        Filament::setCurrentPanel(null);
        expect($livewire->getActionEventListeners($action))->toHaveCount(1);

        config(['filament.broadcasting.echo' => null]);
        expect($livewire->getActionEventListeners($action))->toBeEmpty();
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
                ->afterRecordUpdateReceived(static function (Action $action, Department $record, $livewire, Schema $schema, Schema $form) use ($render): void {
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
        $listeners = $component->instance()->getActionEventListeners($component->instance()->getMountedAction());

        if (! $isEnabled) {
            expect($listeners)->toBeEmpty();
            $component->call('callActionWebSocketListener', 1, 'private:' . ModelChangedBroadcast::getChannel($department) . ',.filament.model-changed')->assertNotFound();

            return;
        }

        $component->mountAction('addNote')->fillForm(['note' => 'Nested draft']);
        $department->update(['name' => 'Changed elsewhere']);

        $component->call('callActionWebSocketListener', ...$listeners[0]['parameters'])->assertReturned(true)
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
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $department->update(['name' => 'Updated elsewhere']);

        $component->call('callActionWebSocketListener', ...$parameters)
            ->assertReturned(true)
            ->assertActionMounted(TestAction::make(ViewAction::class)->table($department))
            ->assertSchemaStateSet(['name' => 'UPDATED ELSEWHERE'])
            ->assertNotDispatched('before-view-filled')
            ->assertNotDispatched('after-view-filled');
    });

    it('signs only the current mounted view record channel', function (): void {
        FilamentRealtime::subscribe(Department::class);
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);

        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(ViewAction::class)->table($department));
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $component->call('authorizeActionRecordUpdates', ...[...$parameters, '123.456'])
            ->assertReturned(['auth' => 'view-test-key:' . hash_hmac('sha256', '123.456:private-' . ModelChangedBroadcast::getChannel($department), 'view-test-secret')])
            ->unmountAction()
            ->call('callActionWebSocketListener', ...$parameters)->assertReturned(false)
            ->call('authorizeActionRecordUpdates', ...[...$parameters, '123.456'])->assertReturned(null)
            ->mountAction(TestAction::make(ViewAction::class)->table($department));

        $department->update(['name' => 'Newer value']);

        $component->call('callActionWebSocketListener', ...$parameters)->assertReturned(true)
            ->call('authorizeActionRecordUpdates', ...[...$parameters, '123.456'])
            ->assertReturned(['auth' => 'view-test-key:' . hash_hmac('sha256', '123.456:private-' . ModelChangedBroadcast::getChannel($department), 'view-test-secret')])
            ->assertSchemaStateSet(['name' => 'Newer value']);

        $replacement = Department::factory()->hasAttached($ticket)->create(['name' => 'Different record']);

        $component->set('mountedActions.0.context.recordKey', (string) $replacement->getKey())
            ->call('authorizeActionRecordUpdates', ...[...$parameters, '123.456'])->assertReturned(null)
            ->assertSchemaStateSet(['name' => 'Newer value']);
        $component->call('callActionWebSocketListener', ...$parameters)->assertNotFound();
    });

    it('rechecks action authorization before refreshing or signing', function (string $method, array $parameters): void {
        FilamentRealtime::subscribe(Department::class);
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create();
        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(ViewAction::class)->table($department));
        $listener = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];

        app()->bind(DepartmentPolicy::class . '::view', static fn (): bool => false);

        $component->call($method, ...[...$listener['parameters'], ...$parameters])->assertForbidden();
    })->with([
        ['callActionWebSocketListener', []],
        ['authorizeActionRecordUpdates', ['123.456']],
    ]);

    it('rejects deleted or out-of-scope view records', function (string $method, array $parameters, bool $isDeleted): void {
        FilamentRealtime::subscribe(Department::class);
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create();
        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(ViewAction::class)->table($department));
        $listener = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];

        if ($isDeleted) {
            $department->forceDelete();
        } else {
            $ticket->departments()->detach($department);
        }

        $component->call($method, ...[...$listener['parameters'], ...$parameters])
            ->assertReturned($method === 'callActionWebSocketListener' ? false : null)
            ->assertActionNotMounted();
    })->with([
        ['callActionWebSocketListener', []],
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
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $component->mountAction('editDetails')->fillForm(['name' => 'Child draft']);
        $department->update(['name' => 'Updated parent']);

        $component->call('callActionWebSocketListener', ...$parameters)->assertReturned(true)
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
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $department->update(['name' => 'Updated child']);

        $component->call('callActionWebSocketListener', ...$parameters)->assertReturned(true)
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
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $department->update(['name' => 'Updated elsewhere']);

        $component->call('callActionWebSocketListener', ...$parameters)
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
        $parameters = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0]['parameters'];

        $department->update(['name' => 'Replacement']);

        $component->call('callActionWebSocketListener', ...$parameters)->assertSchemaStateSet(['name' => 'Mapped Replacement']);
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
                    ->afterRecordUpdateReceived(static fn (Post $record, Schema $schema) => $schema->fillPartially(['latest' => $record->title], ['latest']))
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
                leave() {}
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
});

class WebSocketActions extends Actions
{
    public static bool $isAuthorized = true;

    public static string $listener = 'orders,.OrderShipped';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('review')
                ->modalHeading('Live shipping review')
                ->authorize(static fn (): bool => static::$isAuthorized)
                ->schema([
                    TextInput::make('note'),
                    TextInput::make('latest')->disabled(),
                ])
                ->webSocketListeners(static fn (): array => [
                    static::$listener => static function (array $event, Action $action, Schema $schema, Schema $form, $livewire): void {
                        $schema->fillPartially(['latest' => $event['status']], ['latest']);
                        $livewire->dispatch('shipment-received', sequence: $event['sequence'], action: $action->getName(), path: $schema->getStatePath(), sameSchema: $schema === $form);
                    },
                    'orders,.StatusChanged',
                ])
                ->extraModalFooterActions([
                    Action::make('child')
                        ->schema([TextInput::make('note')])
                        ->webSocketListeners(['orders,.ChildChanged']),
                ]),
        ];
    }
}

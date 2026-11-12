<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\EditConflictHandler;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Events\RecordChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\EditConflictActions;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Pages\EditConflictBrowserTest;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ListPosts;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(EditAction::class)->table($department));
});

it('can mount `EditAction` modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->assertActionMounted(TestAction::make(EditAction::class)->table($department));
});

it('can fill form with record data in `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->assertSchemaStateSet([
            'name' => 'Original Name',
        ]);
});

it('can validate form data in `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => null,
        ])
        ->assertHasFormErrors(['name' => ['required']]);
});

it('can update a record using `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => $newName = Str::random(),
        ])
        ->assertHasNoFormErrors();

    expect($department->refresh()->name)->toBe($newName);
});

describe('edit conflicts', function (): void {
    it('retains the edit modal and rechecks a displayed choice and the subsequent save', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(EditConflictActions::class, ['record' => $post])
            ->mountAction('edit')->fillForm(['title' => 'Draft', 'rating' => 7]);
        $post->update(['title' => 'Saved', 'rating' => 9]);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->assertSet('mountedActions.0.data.title', 'Draft')
            ->assertSet('actionFormConflictStates.0.outcome.conflictingPaths', ['title', 'rating'])
            ->assertNotNotified();

        $post->update(['title' => 'Newer']);
        $component->call('resolveActionFormConflict', 'title', 'draft')
            ->assertSet('actionFormConflictStates.0.base.title', 'Original')
            ->assertSchemaStateSet(['title' => 'Newer'], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'title', 'draft')
            ->assertSet('actionFormConflictStates.0.outcome.conflictingPaths', ['rating'])
            ->call('resolveActionFormConflict', 'rating', 'current')
            ->assertActionMounted('edit')
            ->assertSchemaStateSet(['title' => 'Draft', 'rating' => 9]);

        expect($post->fresh()->title)->toBe('Newer')->and($post->fresh()->rating)->toBe(9);

        $post->update(['title' => 'Latest']);
        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->call('resolveActionFormConflict', 'title', 'draft')
            ->callMountedAction()->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(9);
    });

    it('discards an action draft without saving and captures a fresh baseline when remounted', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(EditConflictActions::class, ['record' => $post])
            ->mountAction('edit')->fillForm(['title' => 'Draft']);
        $post->update(['title' => 'Saved']);

        $component->callMountedAction()->call('discardActionFormChanges')
            ->assertActionMounted('edit')->assertSchemaStateSet(['title' => 'Saved'])
            ->unmountAction()->assertSet('actionFormConflictStates', []);

        $post->update(['title' => 'Remounted']);
        $component->mountAction('edit')->assertActionMounted('edit')->assertSchemaStateSet(['title' => 'Remounted'])
            ->fillForm(['title' => 'New draft'])->callMountedAction()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('New draft');
    });

    it('retains object-valued action arguments across requests when saving a protected edit', function (string $argumentType): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);

        livewire(new class extends EditConflictActions
        {
            public function mount(Post $record, string $argumentType = 'collection'): void
            {
                parent::mount($record);

                $this->mountAction('edit', ['options' => $argumentType === 'collection' ? collect(['locale' => 'en']) : Carbon::parse('2026-01-02T03:04:05.123456Z')]);
            }
        }, ['record' => $post, 'argumentType' => $argumentType])
            ->assertActionMounted('edit')->fillForm(['title' => 'Draft'])
            ->callMountedAction()->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft');
    })->with(['collection', 'carbon']);

    it('combines non-overlapping values and preserves unrepresented nested data', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2, 'json' => ['mine' => 'Original', 'outside' => 'Original']]);
        $component = livewire(EditConflictActions::class, ['record' => $post])
            ->mountAction('edit')->fillForm(['title' => 'Draft', 'json.mine' => 'Mine']);
        $post->update(['rating' => 9, 'json' => ['mine' => 'Original', 'outside' => 'External']]);

        $component->callMountedAction()->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(9)
            ->and($post->fresh()->json)->toEqual(['mine' => 'Mine', 'outside' => 'External']);
    });

    it('uses the configured `fillForm()` mapping for comparisons without replaying fill hooks', function (bool $usesCustomFill): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public bool $usesCustomFill = false;

            public function editAction(): EditAction
            {
                $action = parent::editAction()->schema([
                    TextInput::make('headline')->required(),
                    TextInput::make('score')->numeric(),
                ])->beforeFormFilled(fn () => $this->dispatch('before-edit-fill'))
                    ->afterFormFilled(fn () => $this->dispatch('after-edit-fill'))
                    ->using(static fn (Post $record, array $data): bool => $record->update(['title' => $data['headline'], 'rating' => $data['score']]));

                return $this->usesCustomFill
                    ? $action->fillForm(static fn (Post $record): array => ['headline' => $record->title, 'score' => $record->rating])
                    : $action->mutateRecordDataUsing(static fn (array $data): array => ['headline' => $data['title'], 'score' => $data['rating']]);
            }
        }, ['record' => $post])->set('usesCustomFill', $usesCustomFill)->mountAction('edit')
            ->assertDispatched('before-edit-fill')->assertDispatched('after-edit-fill')
            ->fillForm(['headline' => 'Draft']);
        $post->update(['title' => 'Saved', 'rating' => 9]);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->assertNotDispatched('before-edit-fill')->assertNotDispatched('after-edit-fill')
            ->assertSchemaStateSet(['headline' => 'Saved', 'score' => 9], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'headline', 'draft')
            ->callMountedAction()->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(9);
    })->with([false, true]);

    it('rejects protection without a repeatable `fillForm()` mapping instead of replaying `mountUsing()`', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public function editAction(): EditAction
            {
                return parent::editAction()->mountUsing(static fn (Schema $schema, Post $record) => $schema->fill($record->attributesToArray()));
            }
        }, ['record' => $post])->mountAction('edit')->fillForm(['title' => 'Draft']);

        try {
            $component->callMountedAction();
            test()->fail('Expected non-repeatable form rejection');
        } catch (LogicException $exception) {
            expect($exception->getMessage())->toContain('repeatable fillForm()');
        }

        expect($post->fresh()->title)->toBe('Original')->and(DB::transactionLevel())->toBe($level);
    });

    it('updates untouched action fields while retaining drafts and displayed recovery values', function (): void {
        config(['filament.broadcasting.echo' => ['broadcaster' => 'null']]);
        FilamentRealtime::subscribe(Post::class);
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public function editAction(): EditAction
            {
                return parent::editAction()->receiveRecordUpdates();
            }
        }, ['record' => $post])->mountAction('edit')->fillForm(['title' => 'Draft']);
        $configuration = $component->instance()->getActionEventListeners($component->instance()->getMountedAction())[0];
        $post->update(['title' => 'Saved', 'rating' => 8]);

        $component->call('callActionWebSocketListener', ...$configuration['parameters'])->assertReturned(true)
            ->assertSchemaStateSet(['title' => 'Draft', 'rating' => 8])
            ->assertSet('actionFormConflictStates.0.base.title', 'Original')
            ->assertSet('actionFormConflictStates.0.base.rating', 8)
            ->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts']);
        $post->update(['title' => 'Newer']);

        $component->call('callActionWebSocketListener', ...$configuration['parameters'])->assertReturned(true)
            ->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->assertSet('mountedActions.0.data.title', 'Draft')
            ->assertSchemaStateSet(['title' => 'Saved'], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'title', 'draft')
            ->assertSchemaStateSet(['title' => 'Newer'], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'title', 'draft')->callMountedAction()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft');
    });

    it('stops before validation hooks, dehydration, upload storage and the custom process', function (): void {
        Storage::fake('public');
        EditAction::configureUsing(static function (EditAction $action): void {
            $action->beforeFormValidated(static fn () => throw new RuntimeException('Validation hook ran'))
                ->before(static fn () => throw new RuntimeException('Save hook ran'))
                ->using(static fn () => throw new RuntimeException('Process ran'));
        });

        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public function editAction(): EditAction
            {
                return parent::editAction()->schema([
                    TextInput::make('title')->dehydrateStateUsing(static fn () => throw new RuntimeException('Dehydration ran')),
                    FileUpload::make('json.attachment')->disk('public'),
                ]);
            }
        }, ['record' => $post])->mountAction('edit')->fillForm(['title' => 'Draft', 'json.attachment' => UploadedFile::fake()->create('draft.pdf')]);
        $post->update(['title' => 'Saved']);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])->assertNotNotified();

        expect($post->fresh()->title)->toBe('Saved')->and(Storage::disk('public')->allFiles())->toBe([]);
    });

    it('rechecks authorization before exposing or accepting current action values', function (bool $duringRecovery): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(EditConflictActions::class, ['record' => $post])
            ->mountAction('edit')->fillForm(['title' => 'Draft']);

        if ($duringRecovery) {
            $post->update(['title' => 'Saved']);
            $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts']);
        }

        $post->update(['title' => 'Secret', 'rating' => 10]);

        if ($duringRecovery) {
            $component->call('resolveActionFormConflict', 'title', 'current')->assertForbidden();
        } else {
            $component->callMountedAction()->assertNotNotified()->assertDontSee('Secret');
        }

        expect($post->fresh()->title)->toBe('Secret');
    })->with([false, true]);

    it('retains table relationship scope during conflict recovery', function (): void {
        EditAction::configureUsing(static fn (EditAction $action) => $action->editConflictHandler());
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);
        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(EditAction::class)->table($department))
            ->fillForm(['name' => 'Draft']);
        $department->update(['name' => 'Saved']);
        $component->callMountedAction()->assertSet('actionFormConflictStates.0.outcome.conflictingPaths', ['name']);
        $ticket->departments()->detach($department);

        $component->call('resolveActionFormConflict', 'name', 'draft')->assertActionNotMounted();

        expect($department->fresh()->name)->toBe('Saved');
    });

    it('retains inherited table scope and the parent draft for a nested edit action', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original']);
        $component = livewire(new class extends DepartmentsRelationManager
        {
            public function table(Table $table): Table
            {
                return parent::table($table)->recordActions([
                    Action::make('inspect')->schema([TextInput::make('note')->required()])
                        ->extraModalFooterActions([EditAction::make()->editConflictHandler()]),
                ]);
            }
        }, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make('inspect')->table($department))->fillForm(['note' => 'Keep this'])
            ->mountAction('edit')->fillForm(['name' => 'Draft']);
        $department->update(['name' => 'Saved']);

        $component->callMountedAction()->assertSet('actionFormConflictStates.1.outcome.conflictingPaths', ['name'])
            ->call('resolveActionFormConflict', 'name', 'draft')
            ->assertSet('mountedActions.0.data.note', 'Keep this')
            ->callMountedAction()->assertSet('mountedActions.0.data.note', 'Keep this');
        expect($department->fresh()->name)->toBe('Draft');
    });

    it('reconstructs registered header actions on the isolated form owner', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditPost
        {
            protected function getHeaderActions(): array
            {
                return [EditAction::make()->url(null)->editConflictHandler()->schema([
                    TextInput::make('title')->required(),
                ])];
            }
        }, ['record' => $post->getKey()])->mountAction('edit')->fillForm(['title' => 'Draft']);
        $post->update(['title' => 'Saved']);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->assertSchemaStateSet(['title' => 'Saved'], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'title', 'draft')->callMountedAction();

        expect($post->fresh()->title)->toBe('Draft');
    });

    it('does not substitute an ancestor table record for an explicitly configured child record', function (): void {
        $ancestor = Post::factory()->create(['title' => 'Ancestor']);
        $other = Post::factory()->create(['title' => 'Other']);
        $component = livewire(new class extends ListPosts
        {
            public ?int $otherRecordId = null;

            public function table(Table $table): Table
            {
                return parent::table($table)->recordActions([
                    Action::make('inspect')->requiresConfirmation()->extraModalFooterActions([
                        EditAction::make('editOther')->url(null)
                            ->record(Post::find($this->otherRecordId))->editConflictHandler()
                            ->schema([TextInput::make('title')->required()]),
                    ]),
                ]);
            }
        })->set('otherRecordId', $other->getKey())
            ->mountAction(TestAction::make('inspect')->table($ancestor))
            ->mountAction('editOther')->fillForm(['title' => 'Draft']);
        $other->update(['title' => 'Saved']);

        $component->callMountedAction()->assertSet('actionFormConflictStates.1.outcome.conflictingPaths', ['title'])
            ->call('resolveActionFormConflict', 'title', 'draft')->callMountedAction();

        expect($ancestor->fresh()->title)->toBe('Ancestor')->and($other->fresh()->title)->toBe('Draft');
    });

    it('finalizes only its own transaction when handler configuration changes during saving', function (bool $initiallyEnabled, bool $shouldFail): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public bool $initiallyEnabled = true;

            public bool $shouldFail = false;

            public function editAction(): EditAction
            {
                $action = parent::editAction()->editConflictHandler(fn (Post $record): ?EditConflictHandler => (($record->rating < 8) === $this->initiallyEnabled) ? EditConflictHandler::make() : null);

                if ($this->shouldFail) {
                    $action->after(static fn () => throw new RuntimeException('Save failed'));
                }

                return $action;
            }
        }, ['record' => $post])->set('initiallyEnabled', $initiallyEnabled)->set('shouldFail', $shouldFail)
            ->mountAction('edit')->fillForm(['title' => 'Draft', 'rating' => 9]);

        if ($shouldFail) {
            try {
                $component->callMountedAction();
                test()->fail('Expected save failure');
            } catch (RuntimeException $exception) {
                expect($exception->getMessage())->toBe('Save failed');
            }
        } else {
            $component->callMountedAction()->assertHasNoErrors();
        }

        expect(DB::transactionLevel())->toBe($level);

        if ($initiallyEnabled && $shouldFail) {
            expect($post->fresh()->title)->toBe('Original');
        } else {
            expect($post->fresh()->title)->toBe('Draft');
        }
    })->with([true, false])->with([true, false]);

    it('can configure, replace and disable the handler without enabling ordinary actions', function (): void {
        $handler = EditConflictHandler::make()->ignoredStatePaths(['rating']);
        $action = EditAction::make();

        expect($action->getEditConflictHandler())->toBeNull()
            ->and($action->editConflictHandler()->getEditConflictHandler())->toBeInstanceOf(EditConflictHandler::class)
            ->and($action->editConflictHandler($handler)->getEditConflictHandler())->toBe($handler)
            ->and($action->editConflictHandler(static fn () => $handler)->getEditConflictHandler())->toBe($handler)
            ->and($action->editConflictHandler(false)->getEditConflictHandler())->toBeNull()
            ->and($action->editConflictHandler(null)->getEditConflictHandler())->toBeNull();
    });

    it('repeats a query-returning `resolveRecordUsing()` for recovery and subsequent saves', function (bool $fromGroup, bool $shouldIncludeTrashed): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public bool $fromGroup = false;

            public bool $shouldIncludeTrashed = false;

            public function editAction(): EditAction
            {
                $action = parent::editAction();
                $source = $this->fromGroup ? ActionGroup::make([$action->record(null)])->livewire($this) : $action;
                $shouldIncludeTrashed = $this->shouldIncludeTrashed;
                $source->record((string) $this->record->getKey())
                    ->resolveRecordUsing(static fn (string $key): Builder => Post::query()->withTrashed($shouldIncludeTrashed)->whereKey($key)->where('rating', '<', 5));

                return $action;
            }
        }, ['record' => $post])->set('fromGroup', $fromGroup)->set('shouldIncludeTrashed', $shouldIncludeTrashed);

        if ($shouldIncludeTrashed) {
            $post->delete();
        }

        $component->mountAction('edit')->fillForm(['title' => 'Draft']);
        $post->update(['title' => 'Saved', 'rating' => 3]);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->assertSchemaStateSet(['title' => 'Saved', 'rating' => 3], 'currentActionFormConflictPreview');
        $post->update(['title' => 'Newer']);

        $component->call('resolveActionFormConflict', 'title', 'draft')
            ->assertSchemaStateSet(['title' => 'Newer'], 'currentActionFormConflictPreview')
            ->call('resolveActionFormConflict', 'title', 'draft')->assertActionMounted('edit');
        expect($post->fresh()->title)->toBe('Newer');

        $post->update(['title' => 'Latest']);
        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->call('resolveActionFormConflict', 'title', 'draft')->callMountedAction()
            ->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(3);
    })->with([false, true])->with([false, true]);

    it('rechecks a query-returning parent resolver without losing its draft or source', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public function parentAction(): Action
            {
                return Action::make('parent')->record((string) $this->record->getKey())
                    ->resolveRecordUsing(static fn (string $key): Builder => Post::query()->whereKey($key)->where('rating', '<', 5))
                    ->schema([TextInput::make('note')])
                    ->extraModalFooterActions([$this->editAction()->record(null)]);
            }
        }, ['record' => $post])->mountAction('parent')->fillForm(['note' => 'Keep this'])
            ->mountAction('edit')->fillForm(['title' => 'Draft']);
        $post->update(['title' => 'Saved']);

        $component->callMountedAction()->assertSet('actionFormConflictStates.1.outcome.conflictingPaths', ['title'])
            ->call('resolveActionFormConflict', 'title', 'draft')
            ->assertSet('mountedActions.0.data.note', 'Keep this')
            ->callMountedAction()->assertHasNoErrors()->assertSet('mountedActions.0.data.note', 'Keep this');

        expect($post->fresh()->title)->toBe('Draft');
    });

    it('preserves an explicit record ahead of an unused group resolver during protected saves', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $component = livewire(new class extends EditConflictActions
        {
            public function editAction(): EditAction
            {
                $action = parent::editAction();
                ActionGroup::make([$action])->livewire($this)->record(fn (): Post => $this->record);

                return $action;
            }
        }, ['record' => $post])->mountAction('edit')->fillForm(['title' => 'Draft']);
        $post->update(['title' => 'Saved']);

        $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts'])
            ->call('resolveActionFormConflict', 'title', 'draft')->callMountedAction()
            ->assertHasNoErrors()->assertActionNotMounted();

        expect($post->fresh()->title)->toBe('Draft');
    });

    it('refuses query-resolved records that leave their scope before saving or recovery', function (string $operation): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public function editAction(): EditAction
            {
                return parent::editAction()->record((string) $this->record->getKey())
                    ->resolveRecordUsing(static fn (string $key): Builder => Post::query()->whereKey($key)->where('rating', '<', 5))
                    ->beforeFormValidated(static fn () => throw new RuntimeException('Validation ran'));
            }
        }, ['record' => $post])->mountAction('edit')->fillForm(['title' => 'Draft']);

        if ($operation !== 'save') {
            $post->update(['title' => 'Saved']);
            $component->callMountedAction()->assertActionMounted(['edit', 'resolveFormConflicts']);
        }

        $post->update(['title' => 'Outside scope', 'rating' => 8]);

        $response = match ($operation) {
            'save' => $component->callMountedAction(),
            'choice' => $component->call('resolveActionFormConflict', 'title', 'current'),
            'discard' => $component->call('discardActionFormChanges'),
        };

        $response->assertNotFound();
        expect($post->fresh()->title)->toBe('Outside scope')->and(DB::transactionLevel())->toBe($level);
    })->with(['save', 'choice', 'discard']);

    it('rejects model-returning custom resolution instead of substituting an unscoped locking query', function (string $source): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public string $source = 'callback';

            public function editAction(): EditAction
            {
                $action = parent::editAction();

                if ($this->source === 'group') {
                    $action->record(null);
                    ActionGroup::make([$action])->record(fn (): Post => $this->record)->livewire($this);

                    return $action;
                }

                return ($this->source === 'closure')
                    ? $action->record(fn (): Post => $this->record)
                    : $action->record((string) $this->record->getKey())->resolveRecordUsing(static fn (string $key): ?Post => Post::whereKey($key)->where('rating', '<', 10)->first());
            }
        }, ['record' => $post])->set('source', $source)->mountAction('edit')->fillForm(['title' => 'Draft']);

        try {
            $component->callMountedAction();
            test()->fail('Expected unsupported resolver rejection');
        } catch (LogicException $exception) {
            expect($exception->getMessage())->toContain('scoped locking read');
        }

        expect($post->fresh()->title)->toBe('Original')->and(DB::transactionLevel())->toBe($level);
    })->with(['callback', 'closure', 'group']);

    it('rejects a custom source inherited through a parent action schema', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public function parentAction(): Action
            {
                return Action::make('parent')->record(fn (): Post => $this->record)->schema([
                    Actions::make([$this->editAction()->record(null)])->key('editor'),
                ]);
            }
        }, ['record' => $post])->mountAction('parent')
            ->mountAction(TestAction::make('edit')->schemaComponent('editor'))->fillForm(['title' => 'Draft']);

        try {
            $component->callMountedAction();
            test()->fail('Expected inherited resolver rejection');
        } catch (LogicException $exception) {
            expect($exception->getMessage())->toContain('scoped locking read');
        }

        expect($post->fresh()->title)->toBe('Original')->and(DB::transactionLevel())->toBe($level);
    });

    it('rejects a schema record copied through a parent modal', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $level = DB::transactionLevel();
        $component = livewire(new class extends EditConflictActions
        {
            public function content(Schema $schema): Schema
            {
                return $schema->model(fn (): Post => Post::whereKey($this->record->getKey())->where('rating', '<', 10)->firstOrFail())->components([
                    Actions::make([
                        Action::make('inspect')->requiresConfirmation()->action(static fn () => null)->extraModalFooterActions([
                            $this->editAction()->record(null),
                        ]),
                    ])->key('controls'),
                ]);
            }

            public function render(): string
            {
                return '<div>{{ $this->content }}<x-filament-actions::modals /></div>';
            }
        }, ['record' => $post])->mountAction(TestAction::make('inspect')->schemaComponent('controls', schema: 'content'))
            ->assertActionMounted(TestAction::make('inspect')->schemaComponent('controls', schema: 'content'))
            ->mountAction('edit')->assertActionMounted('edit')->fillForm(['title' => 'Draft']);

        try {
            $component->callMountedAction();
            test()->fail('Expected inherited schema record rejection');
        } catch (LogicException $exception) {
            expect($exception->getMessage())->toContain('scoped locking read');
        }

        expect($post->fresh()->title)->toBe('Original')->and(DB::transactionLevel())->toBe($level);
    });

    it('can recover inside an edit modal in the browser without losing its draft', function (): void {
        Artisan::call('filament:assets');
        config([
            'filament.broadcasting.echo' => ['broadcaster' => 'null'],
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher' => [
                'driver' => 'pusher', 'key' => 'test-key', 'secret' => 'test-secret',
                'app_id' => 'test', 'options' => ['cluster' => 'mt1'],
            ],
        ]);
        Event::fake([RecordChangedBroadcast::class]);
        FilamentRealtime::subscribe(Post::class);
        EditAction::configureUsing(static fn (EditAction $action) => $action->receiveRecordUpdates());
        FilamentView::registerRenderHook(PanelsRenderHook::SCRIPTS_AFTER, static fn (): HtmlString => new HtmlString(<<<'HTML'
            <script>
                window.EchoFactory = class {
                    constructor(configuration) { this.configuration = configuration }
                    private(channelName) {
                        const channel = {
                            listen(event, callback) { window.editRecordNotify = callback; return channel },
                            subscribed: callback => {
                                this.configuration.channelAuthorization.customHandler({ socketId: '123.456' }, (error, authorization) => {
                                    if (error || !authorization?.auth) return
                                    window.editRecordSubscribed = true
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
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $editModal = '[data-testid="conflict-edit-modal"]';
        $comparison = '[data-testid="stale-form-comparison"]';
        $conflictModal = "[role=\"dialog\"]:has({$comparison})";
        $titleInput = "{$editModal} [data-testid=\"edit-title\"]";
        $ratingInput = "{$editModal} [data-testid=\"edit-rating\"]";
        $saveButton = "{$editModal} button[type=\"submit\"]";
        $currentPreview = '[data-testid="stale-form-preview-current"] [data-testid="edit-title"]';

        $page = visit(EditConflictBrowserTest::getUrl(['record' => $post->getKey()]));
        $page->click('[data-testid="conflict-edit-trigger"]')
            ->assertScript('window.editRecordSubscribed', true)
            ->fill($titleInput, 'Draft');

        Post::query()->whereKey($post)->update(['rating' => 5]);
        $page->script('window.editRecordNotify([]); undefined');
        $page->assertValue($ratingInput, '5')->assertValue($titleInput, 'Draft');

        $page->script(<<<'JS'
            window.originalEditFetch = window.fetch
            window.holdEditRefresh = true
            window.fetch = async (...requestArguments) => {
                const response = await window.originalEditFetch(...requestArguments)
                const payload = requestArguments[1]?.body ? JSON.parse(requestArguments[1].body) : null
                if (window.holdEditRefresh && payload?.components?.some(component => component.calls.some(call => call.method === 'callActionWebSocketListener'))) {
                    window.holdEditRefresh = false
                    window.editRefreshHeld = true
                    await new Promise(resolve => window.releaseEditRefresh = resolve)
                }
                return response
            }
            undefined
            JS);
        $post->forceFill(['rating' => 6, 'json' => ['mine' => 'Incoming after typing']])->saveQuietly();
        $page->script('window.editRecordNotify([]); undefined');
        $page->assertScript('window.editRefreshHeld', true)->fill($ratingInput, '7');
        $page->script('window.releaseEditRefresh(); undefined');
        $page->assertValue("{$editModal} input[id$=\".json.mine\"]", 'Incoming after typing')
            ->assertValue($ratingInput, '7')
            ->assertPresent("{$ratingInput}:focus");

        Post::query()->whereKey($post)->update(['title' => 'Saved', 'rating' => 9]);
        $page->click($saveButton)
            ->assertVisible($comparison)
            ->assertValue($currentPreview, 'Saved')
            ->assertDisabled($currentPreview)
            ->assertNoAccessibilityIssues();
        $darkPage = $page->inDarkMode();
        $darkPage->assertNoAccessibilityIssues();

        $post->update(['title' => 'Newer']);
        $page->click('section:has([data-testid="edit-title"]) [data-testid="stale-form-use-draft"]')
            ->assertValue($currentPreview, 'Newer');
        expect($post->fresh()->title)->toBe('Newer');

        $page->click('section:has([data-testid="edit-title"]) [data-testid="stale-form-use-draft"]')
            ->assertMissing($currentPreview)
            ->keys($conflictModal, 'Escape')
            ->assertMissing($conflictModal)
            ->assertValue($titleInput, 'Draft')
            ->click($saveButton)
            ->assertVisible($comparison)
            ->click('[data-testid="stale-form-use-current"]')
            ->assertMissing($comparison)
            ->assertValue($titleInput, 'Draft')
            ->assertValue($ratingInput, '9');
        expect($post->fresh()->title)->toBe('Newer');

        $page->click($saveButton)->assertMissing($editModal);
        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(9);

        $page->click('[data-testid="conflict-edit-trigger"]')->fill($titleInput, 'Discard me');
        $post->update(['title' => 'Latest']);
        $page->click($saveButton)->assertVisible($comparison)
            ->click('[data-testid="stale-form-discard"]')
            ->assertMissing($comparison)->assertValue($titleInput, 'Latest');
        expect($post->fresh()->title)->toBe('Latest');
    });
});

it('can show success notification after updating a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => Str::random(),
        ])
        ->assertNotified();
});

it('does not update record when `EditAction` is cancelled', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->fillForm([
            'name' => 'Changed Name',
        ])
        ->unmountAction()
        ->assertActionNotMounted();

    expect($department->refresh()->name)->toBe('Original Name');
});

it('can edit multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 1']);
    $department2 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 2']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department1), [
            'name' => $newName1 = Str::random(),
        ])
        ->assertHasNoFormErrors()
        ->callAction(TestAction::make(EditAction::class)->table($department2), [
            'name' => $newName2 = Str::random(),
        ])
        ->assertHasNoFormErrors();

    expect($department1->refresh()->name)->toBe($newName1);
    expect($department2->refresh()->name)->toBe($newName2);
});

it('can set `mutateRecordDataUsing()` callback and returns fluent `$this`', function (): void {
    $action = EditAction::make();
    $callback = static fn (array $data): array => array_merge($data, ['extra' => 'value']);

    $result = $action->mutateRecordDataUsing($callback);

    expect($result)->toBe($action);
});

it('can clear `mutateRecordDataUsing()` with `null`', function (): void {
    $action = EditAction::make()
        ->mutateRecordDataUsing(static fn (array $data): array => $data)
        ->mutateRecordDataUsing(null);

    expect($action)->toBeInstanceOf(EditAction::class);
});

it('has `edit` as default name', function (): void {
    expect(EditAction::getDefaultName())->toBe('edit');
});

it('preserves original record when `EditAction` is cancelled after filling form', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Do Not Change']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->fillForm([
            'name' => 'Attempted Change',
        ])
        ->unmountAction()
        ->assertActionNotMounted();

    expect($department->refresh()->name)->toBe('Do Not Change');
});

it('returns fluent `$this` from `using()`', function (): void {
    $action = EditAction::make();

    expect($action->using(static fn () => null))->toBe($action);
});

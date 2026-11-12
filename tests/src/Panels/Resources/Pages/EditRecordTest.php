<?php

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\EditConflictHandler;
use Filament\Notifications\Notification;
use Filament\Resources\Events\RecordSaved;
use Filament\Resources\Events\RecordUpdated;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Schemas\StateComparison;
use Filament\Support\Events\RecordChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages\EditUserPost;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\DatabaseTransactionsManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(PostResource::getUrl('edit', [
        'record' => Post::factory()->create(),
    ]))->assertSuccessful();
});

it('can retrieve data', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->assertSchemaStateSet([
            'author_id' => $post->author->getKey(),
            'content' => $post->content,
            'tags' => $post->tags,
            'title' => $post->title,
            'rating' => $post->rating,
        ]);
});

it('can save', function (): void {
    Event::fake([
        RecordUpdated::class,
        RecordSaved::class,
    ]);

    $post = Post::factory()->create();
    $newData = Post::factory()->make();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'author_id' => $newData->author->getKey(),
            'content' => $newData->content,
            'tags' => $newData->tags,
            'title' => $newData->title,
            'rating' => $newData->rating,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->author->toBeSameModel($newData->author)
        ->content->toBe($newData->content)
        ->tags->toBe($newData->tags)
        ->title->toBe($newData->title);

    Event::assertDispatched(RecordUpdated::class);
    Event::assertDispatched(RecordSaved::class);
});

describe('stale edits', function (): void {
    it('keeps existing save behavior without opting in to a handler', function (): void {
        $post = Post::factory()->create(['title' => 'Original']);
        $page = livewire(new class extends EditRecord
        {
            protected static string $resource = PostResource::class;
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft']);

        $post->update(['title' => 'Saved']);
        $page->call('save')->assertHasNoFormErrors()->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Draft');
    });

    it('updates untouched fields on record refresh without accepting local edits or saving', function (): void {
        config(['filament.broadcasting.echo' => ['broadcaster' => 'null']]);
        Event::fake([RecordChangedBroadcast::class]);
        FilamentRealtime::subscribe(Post::class);
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])
            ->fillForm(['title' => 'My draft']);

        $post->update(['title' => 'Their title', 'rating' => 8]);

        $page->call('refreshRecordPage')
            ->assertSchemaStateSet(['title' => 'My draft', 'rating' => 8])
            ->assertSet('baseFormState.title', 'Original')
            ->assertSet('baseFormState.rating', 8)
            ->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Their title');

        $page->fillForm(['rating' => 6]);
        $post->update(['rating' => 9]);
        $page->call('refreshRecordPage')
            ->assertSchemaStateSet(['title' => 'My draft', 'rating' => 6])
            ->assertSet('baseFormState.rating', 8)
            ->call('save')
            ->assertSet('staleFormState.conflictingPaths', ['title', 'rating']);

        expect($post->fresh()->rating)->toBe(9);
    });

    it('retains locally edited groups and ignored values during live updates', function (): void {
        config(['filament.broadcasting.echo' => ['broadcaster' => 'null']]);
        Event::fake([RecordChangedBroadcast::class]);
        FilamentRealtime::subscribe(Post::class);
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2, 'content' => 'Original content']);
        $page = livewire(new class extends EditPost
        {
            protected function getFormEditConflictHandler(): ?EditConflictHandler
            {
                return EditConflictHandler::make()
                    ->statePathGroups(['details' => ['title', 'rating'], 'content' => ['content']])
                    ->ignoredStatePaths(['content']);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft', 'content' => 'Locally regenerated']);

        $post->update(['rating' => 8, 'content' => 'Regenerated elsewhere']);
        $page->call('refreshRecordPage')
            ->assertSchemaStateSet(['title' => 'Draft', 'rating' => 2, 'content' => 'Locally regenerated'])
            ->assertSet('baseFormState.rating', 2)
            ->assertSet('baseFormState.content', 'Original content');
    });

    it('combines non-overlapping changes and identical concurrent edits without a conflict', function (bool $saveSection): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2, 'json' => ['mine' => 'Original', 'theirs' => 'Original', 'outside_form' => 'Original']]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    Section::make('Details')->key('details')->schema([
                        TextInput::make('title'),
                        TextInput::make('rating')->numeric()->required(),
                        TextInput::make('json.mine'),
                        TextInput::make('json.theirs'),
                    ])->footerActions([
                        Action::make('saveSection')->action(static fn (Section $component, EditRecord $livewire) => $livewire->saveFormComponentOnly($component)),
                    ]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Same edit', 'json.mine' => 'My draft']);

        $post->update(['title' => 'Same edit', 'rating' => 9, 'json' => ['mine' => 'Original', 'theirs' => 'Their edit', 'outside_form' => 'External value']]);

        if ($saveSection) {
            $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'));
        } else {
            $page->call('save');
        }

        $page->assertHasNoFormErrors()->assertSet('staleFormState', null)
            ->assertSchemaStateSet(['rating' => 9, 'json.theirs' => 'Their edit']);

        expect($post->fresh()->title)->toBe('Same edit')
            ->and($post->fresh()->rating)->toBe(9)
            ->and($post->fresh()->json)->toEqual(['mine' => 'My draft', 'theirs' => 'Their edit', 'outside_form' => 'External value']);
    })->with([false, true]);

    it('limits section saves and baseline acceptance to the saved fields', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    TextInput::make('title'),
                    Section::make('Details')->key('details')->schema([
                        TextInput::make('rating'),
                    ])->footerActions([
                        Action::make('saveSection')->action(static fn (Section $component, EditRecord $livewire) => $livewire->saveFormComponentOnly($component)),
                    ]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'My title', 'rating' => 7]);
        $post->update(['title' => 'Their title']);

        $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'))
            ->assertSet('staleFormState', null)
            ->assertSchemaStateSet(['title' => 'My title']);

        expect($post->fresh()->title)->toBe('Their title')->and($post->fresh()->rating)->toBe(7);

        $page->call('save')->assertSet('staleFormState.conflictingPaths', ['title']);

        expect($post->fresh()->title)->toBe('Their title');
    });

    it('validates incoming values before saving the combined form', function (): void {
        TextInput::configureUsing(static function (TextInput $component): void {
            if ($component->getName() === 'rating') {
                $component->maxValue(5);
            }
        });

        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['title' => 'My draft']);
        $post->update(['rating' => 9]);

        $page->call('save')->assertHasFormErrors(['rating' => 'max'])
            ->assertSet('staleFormState', null)->assertSchemaStateSet(['title' => 'My draft']);

        expect($post->fresh()->title)->toBe('Original')->and($post->fresh()->rating)->toBe(9);

        $post->update(['rating' => 4]);
        $page->call('save')->assertHasNoFormErrors()->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('My draft')->and($post->fresh()->rating)->toBe(4);
    });

    it('checks section group members and rejects section saves that split a group', function (bool $shouldSplitGroup): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            public bool $shouldSplitGroup = false;

            protected function getFormStateComparison(): StateComparison
            {
                return parent::getFormStateComparison()->statePathGroups(['details' => ['title', 'rating']]);
            }

            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    Section::make('Details')->key('details')->schema([
                        TextInput::make('title'),
                        ...($this->shouldSplitGroup ? [] : [TextInput::make('rating')]),
                    ])->footerActions([
                        Action::make('saveSection')->action(static fn (Section $component, EditRecord $livewire) => $livewire->saveFormComponentOnly($component)),
                    ]),
                    ...($this->shouldSplitGroup ? [TextInput::make('rating')] : []),
                ]);
            }
        }, ['record' => $post->getKey(), 'shouldSplitGroup' => $shouldSplitGroup])->fillForm(['title' => 'My title']);
        $post->update(['title' => 'Their title']);

        if ($shouldSplitGroup) {
            expect(fn () => $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form')))
                ->toThrow(LogicException::class, 'A section save cannot split a form state comparison group.');
        } else {
            $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'))
                ->assertSet('staleFormState.conflictingPaths', ['details']);
        }

        expect($post->fresh()->title)->toBe('Their title');
    })->with([false, true]);

    it('refreshes a conflict choice when its group membership changes', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            public bool $combineFields = false;

            protected function getFormStateComparison(): StateComparison
            {
                return parent::getFormStateComparison()->statePathGroups($this->combineFields
                    ? ['details' => ['title', 'rating']]
                    : ['details' => ['title'], 'rating' => ['rating']]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'My title', 'rating' => 7]);
        $post->update(['title' => 'Their title', 'rating' => 9]);

        $page->call('save')->assertSet('staleFormState.conflictingPaths', ['details', 'rating'])
            ->set('combineFields', true)
            ->call('resolveFormConflict', 'details', 'draft')
            ->assertSet('baseFormState.title', 'Original')
            ->assertSet('baseFormState.rating', 2)
            ->assertSet('staleFormState.statePathGroups', ['details' => ['title', 'rating']])
            ->assertSet('staleFormState.conflictingPaths', ['details']);

        expect($post->fresh()->title)->toBe('Their title')->and($post->fresh()->rating)->toBe(9);

        $page->call('resolveFormConflict', 'details', 'current')->call('save')->assertHasNoFormErrors()
            ->assertSchemaStateSet(['title' => 'Their title', 'rating' => 9]);

        expect($post->fresh()->title)->toBe('Their title')->and($post->fresh()->rating)->toBe(9);
    });

    it('resolves each conflict independently and rejects choices for values that changed since display', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2, 'content' => 'Original content']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['title' => 'My title', 'rating' => 7]);
        $post->update(['title' => 'Their title', 'rating' => 9, 'content' => 'Their content']);

        $page->call('save')->assertSet('staleFormState.conflictingPaths', ['title', 'rating']);
        $post->update(['title' => 'Newer title']);

        $page->call('resolveFormConflict', 'title', 'draft')
            ->assertSet('baseFormState.title', 'Original')
            ->assertSet('staleFormState.current.title', 'Newer title')
            ->assertSchemaStateSet(['title' => 'My title'], 'form');

        $page->call('resolveFormConflict', 'title', 'draft')
            ->assertSet('staleFormState.conflictingPaths', ['rating'])
            ->call('resolveFormConflict', 'rating', 'current')
            ->assertSet('staleFormState', null)
            ->assertSchemaStateSet(['title' => 'My title', 'rating' => 9]);

        expect($post->fresh()->title)->toBe('Newer title');

        $page->call('save')->assertHasNoFormErrors();

        expect($post->fresh()->title)->toBe('My title')
            ->and($post->fresh()->rating)->toBe(9)
            ->and($post->fresh()->content)->toBe('Their content');
    });

    it('rejects a stale save before hooks or writes and retains the base and draft', function (bool $shouldUseFormWrapper, string $contentLayout): void {
        Event::fake([RecordUpdated::class, RecordSaved::class]);

        TextInput::configureUsing(static function (TextInput $component): void {
            if ($component->getName() === 'title') {
                $component->label(new class implements Htmlable
                {
                    public function toHtml(): string
                    {
                        return '<strong>Post title" onmouseover=alert(1) data-extra="</strong>';
                    }
                });
            }
        });

        $post = Post::factory()->create(['title' => 'Original title']);
        $originalAuthorName = $post->author->name;

        $page = livewire(new class extends EditPost
        {
            public bool $shouldUseFormWrapper = true;

            public string $contentLayout = 'plain';

            public function hasFormWrapper(): bool
            {
                return $this->shouldUseFormWrapper;
            }

            public function hasCombinedRelationManagerTabsWithContent(): bool
            {
                return $this->contentLayout === 'combined';
            }

            public function content(Schema $schema): Schema
            {
                if ($this->contentLayout === 'wrapped') {
                    return $schema->components([Group::make([$this->getFormContentComponent()])->key('custom-layout')]);
                }

                return parent::content($schema);
            }

            protected function beforeValidate(): void
            {
                $this->getRecord()->author->update(['name' => 'Save hook ran']);
            }
        }, ['record' => $post->getKey(), 'shouldUseFormWrapper' => $shouldUseFormWrapper, 'contentLayout' => $contentLayout])->fillForm(['title' => 'My draft']);

        Post::query()->whereKey($post)->update(['title' => 'Changed with a query']);

        $transactionLevel = DB::transactionLevel();

        $page->call('save')
            ->assertHasNoFormErrors(form: 'form')
            ->assertSchemaStateSet(['title' => 'My draft'], 'form')
            ->assertSet('baseFormState.title', 'Original title')
            ->assertSet('staleFormState.base.title', 'Original title')
            ->assertSet('staleFormState.draft.title', 'My draft')
            ->assertSet('staleFormState.current.title', 'Changed with a query')
            ->assertSet('staleFormState.changedPaths', ['title'])
            ->assertNoRedirect()
            ->assertNotNotified()
            ->assertActionMounted()
            ->assertSee('aria-label="Keep my changes for Post title&quot; onmouseover=alert(1) data-extra=&quot;"', escape: false)
            ->assertSchemaStateSet(['title' => 'My draft'], 'draftFormConflictPreview')
            ->assertSchemaStateSet(['title' => 'Changed with a query'], 'currentFormConflictPreview');

        expect($post->fresh()->title)->toBe('Changed with a query')
            ->and($post->author->fresh()->name)->toBe($originalAuthorName)
            ->and(DB::transactionLevel())->toBe($transactionLevel);

        Event::assertNotDispatched(RecordUpdated::class);
        Event::assertNotDispatched(RecordSaved::class);
    })->with([
        'without form wrapper' => [false, 'plain'],
        'with form wrapper' => [true, 'plain'],
        'combined relation manager tabs' => [true, 'combined'],
        'custom content wrapper' => [true, 'wrapped'],
    ]);

    it('isolates configured field previews from the draft, callbacks and conflict choices', function (): void {
        $post = Post::factory()->create([
            'title' => 'Original',
            'content' => '<p>Original content</p>',
            'tags' => [['title' => 'Original item']],
        ]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    TextInput::make('title')->id('custom-title')
                        ->afterStateUpdated(static function (Post $record, string $state): void {
                            $record->author->update(['name' => $state]);
                        })
                        ->suffixAction(Action::make('changeTitle')->action(static function (): void {
                            throw new RuntimeException('Preview actions must not run.');
                        })),
                    RichEditor::make('content'),
                    Repeater::make('tags')->schema([TextInput::make('title')]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm([
            'title' => 'My draft',
            'content' => '<p>My content</p>',
            'tags' => [['title' => 'My item']],
        ]);
        $post->update([
            'title' => 'Saved title',
            'content' => '<p>Saved content</p>',
            'tags' => [['title' => 'Saved item']],
        ]);

        $page->call('save')
            ->assertSchemaStateSet(static function (array $state): array {
                expect(array_values($state['tags']))->toBe([['title' => 'My item']]);

                return ['title' => 'My draft', 'content' => '<p>My content</p>'];
            }, 'draftFormConflictPreview')
            ->assertSchemaStateSet(static function (array $state): array {
                expect(array_values($state['tags']))->toBe([['title' => 'Saved item']]);

                return ['title' => 'Saved title', 'content' => '<p>Saved content</p>'];
            }, 'currentFormConflictPreview')
            ->assertFormFieldDisabled('title', 'draftFormConflictPreview')
            ->assertFormFieldDisabled('content', 'currentFormConflictPreview')
            ->assertActionDisabled(TestAction::make('changeTitle')->schemaComponent('title', 'draftFormConflictPreview'))
            ->assertSet('formConflictPreviewState.current.author_id', null)
            ->call('enableSchemaStateUpdateHooksForTesting')
            ->set('formConflictPreviewState.draft.title', 'Forged preview')
            ->set('formConflictPreviewState.current.title', 'Forged saved preview')
            ->assertSchemaStateSet(['title' => 'My draft'], 'form')
            ->assertSet('staleFormState.draft.title', 'My draft')
            ->assertSet('staleFormState.current.title', 'Saved title');

        expect($post->author->fresh()->name)->toBe('My draft');

        $page->call('resolveFormConflict', 'title', 'draft')
            ->call('resolveFormConflict', 'content', 'current')
            ->call('resolveFormConflict', 'tags', 'draft')
            ->call('save')->assertHasNoFormErrors();

        expect($post->fresh()->title)->toBe('My draft')
            ->and($post->fresh()->content)->toBe('<p>Saved content</p>')
            ->and($post->fresh()->tags)->toBe([['title' => 'My item']]);
    });

    it('customizes conflict schemas and previews for grouped form values mapped to record attributes', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([TextInput::make('details.title'), TextInput::make('details.rating')]);
            }

            protected function mutateFormDataBeforeFill(array $data): array
            {
                return ['details' => ['title' => $data['title'], 'rating' => $data['rating']]];
            }

            protected function mutateFormDataBeforeSave(array $data): array
            {
                return $data['details'];
            }

            protected function getFormStateComparison(): StateComparison
            {
                return parent::getFormStateComparison()->statePathGroups(['summary' => ['details.title', 'details.rating']]);
            }

            protected function getFormEditConflictHandler(): ?EditConflictHandler
            {
                return (new class extends EditConflictHandler
                {
                    protected function getSchemaComponents(): array
                    {
                        return [Text::make('Review these details together.'), ...parent::getSchemaComponents()];
                    }

                    protected function getLabel(string $statePath): string | Htmlable
                    {
                        return 'Post details';
                    }

                    protected function getPreviewComponent(string $statePath, string $source): Component
                    {
                        return Group::make([
                            Text::make($source === 'draft' ? 'Your proposed details' : 'Currently saved details'),
                            Text::make('Retained title: ' . $this->getOutcome()[$source]['details']['title']),
                            parent::getPreviewComponent($statePath, $source),
                        ]);
                    }

                    protected function getChoiceAction(string $statePath, string $source): Action
                    {
                        return parent::getChoiceAction($statePath, $source)
                            ->label($source === 'draft' ? 'Keep draft details' : 'Use saved details');
                    }
                })->statePathGroups(['summary' => ['details.title', 'details.rating']]);
            }
        }, ['record' => $post->getKey()])->fillForm(['details.title' => 'Draft', 'details.rating' => 7]);
        $post->update(['title' => 'Saved', 'rating' => 9]);

        $page->call('save')
            ->assertSet('staleFormState.conflictingPaths', ['summary'])
            ->assertSee('Review these details together.')
            ->assertSee('Post details')
            ->assertSee('Your proposed details')
            ->assertSee('Currently saved details')
            ->assertSee('Keep draft details')
            ->assertSee('Use saved details')
            ->assertSchemaStateSet(['details.title' => 'Draft', 'details.rating' => 7], 'draftFormConflictPreview')
            ->assertSchemaStateSet(['details.title' => 'Saved', 'details.rating' => 9], 'currentFormConflictPreview');

        expect($post->fresh()->title)->toBe('Saved')->and($post->fresh()->rating)->toBe(9);

        $post->update(['title' => 'Newer saved']);
        $page->call('resolveFormConflict', 'summary', 'draft')
            ->assertSet('baseFormState.details.title', 'Original')
            ->assertSet('staleFormState.current.details.title', 'Newer saved')
            ->assertSee('Retained title: Newer saved');

        $page->call('resolveFormConflict', 'summary', 'draft')->call('save')->assertHasNoFormErrors();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(7);
    });

    it('does not save relationships or dehydrate fields when rejecting a stale save', function (): void {
        $post = Post::factory()->create(['title' => 'Original']);
        $originalAuthorName = $post->author->name;
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    TextInput::make('title')->beforeStateDehydrated(static function (Post $record): void {
                        $record->author->update(['name' => 'Dehydration ran']);
                    }),
                    Section::make('Author')->relationship('author')->schema([TextInput::make('name')]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft', 'author' => ['name' => 'Draft author']]);

        $post->update(['title' => 'External edit']);
        $page->call('save')->assertSchemaStateSet(['title' => 'Draft', 'author.name' => 'Draft author'], 'form');

        expect($post->fresh()->title)->toBe('External edit')
            ->and($post->author->fresh()->name)->toBe($originalAuthorName);

        $page->call('resolveFormConflict', 'title', 'draft')->call('save');

        expect($post->fresh()->title)->toBe('Draft')
            ->and($post->author->fresh()->name)->toBe('Draft author');
    });

    it('protects auxiliary file names without a separate field', function (bool $saveSection, bool $isMultiple): void {
        FileUpload::configureUsing(static fn (FileUpload $component) => $component->multiple($isMultiple));
        $attachment = $isMultiple ? ['report.pdf'] : 'report.pdf';
        $originalFilename = $isMultiple ? ['report.pdf' => 'Original.pdf'] : 'Original.pdf';
        $draftFilename = $isMultiple ? ['report.pdf' => 'Draft.pdf'] : 'Draft.pdf';
        $currentFilename = $isMultiple ? ['report.pdf' => 'External.pdf'] : 'External.pdf';
        $reviewedFilename = $isMultiple ? ['report.pdf' => 'Reviewed.pdf'] : 'Reviewed.pdf';
        $post = Post::factory()->create(['title' => 'Original', 'json' => ['attachment' => $attachment, 'filename' => $originalFilename]]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    Section::make('Details')
                        ->key('details')
                        ->schema([
                            TextInput::make('title'),
                            FileUpload::make('json.attachment')->storeFileNamesIn('json.filename')->fetchFileInformation(false),
                        ])
                        ->footerActions([
                            Action::make('saveSection')->action(static fn (Section $component, EditRecord $livewire) => $livewire->saveFormComponentOnly($component)),
                        ]),
                ]);
            }
        }, ['record' => $post->getKey()])
            ->fillForm(['title' => 'Draft'])
            ->set('data.json.filename', $draftFilename);

        $post->update(['json' => ['attachment' => $attachment, 'filename' => $currentFilename]]);

        if ($saveSection) {
            $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'));
        } else {
            $page->call('save');
        }

        $page->assertSet('staleFormState.changedPaths', [$isMultiple ? 'json.filename.report.pdf' : 'json.filename'])
            ->assertSet('staleFormState.base.json.filename', $originalFilename)
            ->assertSet('staleFormState.current.json.filename', $currentFilename)
            ->assertSchemaStateSet(['title' => 'Draft'], 'form');

        $page->assertActionMounted(TestAction::make('resolveFormConflicts')->schemaComponent('form-actions', 'content'));
        $page->assertDontSee('Original.pdf')
            ->assertSet('formConflictPreviewState.draft.json.filename', $draftFilename)
            ->assertSet('formConflictPreviewState.current.json.filename', $currentFilename);

        expect($post->fresh()->json['filename'])->toBe($currentFilename)
            ->and($post->fresh()->title)->toBe('Original');

        $page->call('resolveFormConflict', 'json.attachment', 'draft')->set('data.json.filename', $reviewedFilename);

        if ($saveSection) {
            $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'));
        } else {
            $page->call('save');
        }

        $page->assertSet('staleFormState', null)->assertHasNoFormErrors();

        expect($post->fresh()->json)->toEqual(['attachment' => $attachment, 'filename' => $reviewedFilename])
            ->and($post->fresh()->title)->toBe('Draft');
    })->with([false, true])->with([false, true]);

    it('binds upload choices to the displayed temporary file without storing rejected uploads', function (): void {
        Storage::fake('local');
        Storage::disk('local')->put('original.pdf', 'Original');
        Storage::disk('local')->put('external.pdf', 'External');
        $post = Post::factory()->create(['json' => ['attachment' => 'original.pdf']]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    FileUpload::make('json.attachment')->disk('local')->directory('stored')->preserveFilenames()->fetchFileInformation(false),
                ]);
            }
        }, ['record' => $post->getKey()])->set('data.json.attachment', [])
            ->fillForm(['json.attachment' => UploadedFile::fake()->create('draft-a.pdf', 1)]);

        $post->update(['json' => ['attachment' => 'external.pdf']]);
        $page->call('save')->assertSet('staleFormState.conflictingPaths', ['json.attachment']);

        $page->set('data.json.attachment', [])->fillForm(['json.attachment' => UploadedFile::fake()->create('draft-b.pdf', 1)], 'form')
            ->call('resolveFormConflict', 'json.attachment', 'draft')
            ->assertSet('baseFormState.json.attachment', 'original.pdf')
            ->assertSet('staleFormState.draft.json.attachment.name', 'draft-b.pdf');

        expect(Storage::disk('local')->allFiles('stored'))->toBe([])
            ->and($post->fresh()->json['attachment'])->toBe('external.pdf');

        $page->call('resolveFormConflict', 'json.attachment', 'draft')->call('save')->assertHasNoFormErrors();

        expect($post->fresh()->json['attachment'])->toBe('stored/draft-b.pdf');
        Storage::disk('local')->assertExists('stored/draft-b.pdf');
        Storage::disk('local')->assertMissing('stored/draft-a.pdf');
    });

    it('treats reordering a `KeyValue` as a whole-field edit', function (bool $shouldUseSimpleRepeater): void {
        Event::fake([RecordSaved::class]);

        // Keep the existing `settings` entry so only row ordering changes.
        $baseTags = $shouldUseSimpleRepeater ? [['first' => 'A', 'second' => 'B', 'settings' => []]] : [['settings' => ['first' => 'A', 'second' => 'B']]];
        $draftTags = $shouldUseSimpleRepeater ? [['second' => 'B', 'first' => 'A', 'settings' => []]] : [['settings' => ['second' => 'B', 'first' => 'A']]];
        $currentTags = $shouldUseSimpleRepeater ? [['first' => 'External', 'second' => 'B', 'settings' => []]] : [['settings' => ['first' => 'External', 'second' => 'B']]];
        $post = Post::factory()->create([
            'json' => ['first' => 'A', 'second' => 'B'],
            'tags' => $baseTags,
        ]);
        $page = livewire(new class extends EditPost
        {
            public bool $shouldUseSimpleRepeater = false;

            public function form(Schema $schema): Schema
            {
                $settings = KeyValue::make('settings')->reorderable();

                return $schema->components([
                    KeyValue::make('json')->reorderable(),
                    $this->shouldUseSimpleRepeater ? Repeater::make('tags')->simple($settings) : Repeater::make('tags')->schema([$settings]),
                ]);
            }
        }, ['record' => $post->getKey(), 'shouldUseSimpleRepeater' => $shouldUseSimpleRepeater])->fillForm([
            'json' => ['second' => 'B', 'first' => 'A'],
            'tags' => $shouldUseSimpleRepeater ? [['settings' => $draftTags[0]]] : $draftTags,
        ]);

        $post->update([
            'json' => ['first' => 'External', 'second' => 'B'],
            'tags' => $currentTags,
        ]);
        $page->call('save')->assertSet('staleFormState.draft.tags', $draftTags)
            ->assertSet('staleFormState.conflictingPaths', ['json', 'tags']);

        expect($post->fresh()->json)->toBe(['first' => 'External', 'second' => 'B']);

        $page->call('resolveFormConflict', 'json', 'draft')->call('resolveFormConflict', 'tags', 'draft')
            ->call('save')->assertHasNoFormErrors();

        Event::assertDispatched(RecordSaved::class, static fn (string $event, array $payload): bool => ($payload['data']['json'] === ['second' => 'B', 'first' => 'A']) &&
            ($payload['data']['tags'] === $draftTags));

        expect($post->fresh()->json)->toEqual(['second' => 'B', 'first' => 'A'])
            ->and($post->fresh()->tags)->toEqual($draftTags);

        if (in_array(DB::connection()->getDriverName(), ['sqlite', 'sqlsrv'])) {
            expect($post->fresh()->json)->toBe(['second' => 'B', 'first' => 'A'])
                ->and($post->fresh()->tags)->toBe($draftTags);
        }
    })->with([false, true]);

    it('cleans up failed commits without undoing successful commits', function (bool $saveSection, bool $shouldFailAfterCommit): void {
        $originalConnection = DB::getDefaultConnection();
        config()->set('database.connections.edit_record_commits', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'foreign_key_constraints' => true,
        ]);
        DB::setDefaultConnection('edit_record_commits');
        $connection = DB::connection();
        // Exercise a real commit, outside `RefreshDatabase`'s wrapping transaction.
        $connection->setTransactionManager(app(DatabaseTransactionsManager::class));

        try {
            $connection->statement('CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT)');
            $connection->statement('CREATE TABLE parents (id INTEGER PRIMARY KEY)');
            $connection->statement('CREATE TABLE children (parent_id INTEGER REFERENCES parents (id) DEFERRABLE INITIALLY DEFERRED)');
            $post = Post::query()->create(['title' => 'Original']);
            $page = livewire(new class extends EditPost
            {
                public bool $shouldFailAfterCommit = false;

                public function form(Schema $schema): Schema
                {
                    return $schema->components([
                        Section::make('Details')
                            ->key('details')
                            ->schema([TextInput::make('title')])
                            ->footerActions([
                                Action::make('saveSection')->action(static fn (Section $component, EditRecord $livewire) => $livewire->saveFormComponentOnly($component)),
                            ]),
                    ]);
                }

                protected function afterSave(): void
                {
                    if ($this->shouldFailAfterCommit) {
                        DB::afterCommit(static function (): void {
                            throw new RuntimeException('After commit failed');
                        });

                        return;
                    }

                    DB::insert('INSERT INTO children (parent_id) VALUES (999)');
                }
            }, ['record' => $post->getKey()])
                ->set('shouldFailAfterCommit', $shouldFailAfterCommit)
                ->fillForm(['title' => 'Draft']);

            expect(fn () => $saveSection
                ? $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'))
                : $page->call('save'))
                ->toThrow($shouldFailAfterCommit ? RuntimeException::class : PDOException::class);

            expect($connection->transactionLevel())->toBe(0)
                ->and($connection->getPdo()->inTransaction())->toBeFalse()
                ->and($post->fresh()->title)->toBe($shouldFailAfterCommit ? 'Draft' : 'Original')
                ->and($connection->table('children')->count())->toBe(0);
        } finally {
            while ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            DB::setDefaultConnection($originalConnection);
            DB::purge('edit_record_commits');
        }
    })->with([false, true])->with([false, true]);

    it('rechecks after reviewing current values and allows a revised retry', function (): void {
        $post = Post::factory()->create(['title' => 'Original title']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['title' => 'My draft']);

        $post->update(['title' => 'First external edit']);
        $page->call('save')->call('resolveFormConflict', 'title', 'draft')->assertSchemaStateSet(['title' => 'My draft']);

        $post->update(['title' => 'Second external edit']);
        $page->fillForm(['title' => 'My revised draft'])
            ->call('save')
            ->assertSet('staleFormState.current.title', 'Second external edit');

        expect($post->fresh()->title)->toBe('Second external edit');

        $page->call('resolveFormConflict', 'title', 'draft')->call('save')->assertSet('staleFormState', null)->assertHasNoFormErrors();

        expect($post->fresh()->title)->toBe('My revised draft');

        $page->fillForm(['title' => 'Another edit'])->call('save')->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Another edit');
    });

    it('discards the draft only when requested and reloads the latest values', function (): void {
        $post = Post::factory()->create(['title' => 'Original']);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['title' => 'Draft']);

        $post->update(['title' => 'First current']);
        $page->call('save');
        $post->update(['title' => 'Latest current']);

        $page->call('discardFormChanges')
            ->assertSchemaStateSet(['title' => 'Latest current'])
            ->assertSet('baseFormState.title', 'Latest current')
            ->assertSet('staleFormState', null)
            ->fillForm(['title' => 'New draft'])
            ->call('save');

        expect($post->fresh()->title)->toBe('New draft');
    });

    it('ignores configured form paths without disabling saving those values', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            protected function getFormStateComparison(): StateComparison
            {
                return parent::getFormStateComparison()
                    ->ignoredStatePaths(['rating']);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft', 'rating' => 7]);

        $post->update(['rating' => 4]);
        $page->call('save')->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(7);

        $post->update(['title' => 'External title']);
        $page->fillForm(['title' => 'Another draft'])->call('save')->assertSet('staleFormState.changedPaths', ['title']);

        expect($post->fresh()->title)->toBe('External title');

        $post->update(['rating' => 9]);
        $page->call('save')
            ->assertSet('staleFormState.changedPaths', ['title'])
            ->assertSet('staleFormState.ignoredStatePaths', ['rating'])
            ->assertSet('staleFormState.current.rating', 9)
            ->assertSet('data.title', 'Another draft');
    });

    it('does not treat non-form record changes as stale', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'is_published' => false]);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['title' => 'Draft']);

        $post->update(['is_published' => true]);
        $page->call('save')->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->is_published)->toBeTrue();
    });

    it('compares repeatable values without their transient item keys', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'tags' => [['label' => 'First'], ['label' => 'Second']]]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    TextInput::make('title'),
                    Repeater::make('tags')->schema([TextInput::make('label')]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft']);

        $page->call('save')->assertSet('staleFormState', null);

        expect($post->fresh()->title)->toBe('Draft');

        $page->fillForm(['tags' => [['label' => 'My first'], ['label' => 'Second']]]);
        $post->update(['tags' => [['label' => 'First'], ['label' => 'External']]]);
        $page->call('save')->assertSet('staleFormState.conflictingPaths', ['tags']);

        expect($post->fresh()->tags[1]['label'])->toBe('External');

        $page->call('resolveFormConflict', 'tags', 'current')->call('save')->assertHasNoFormErrors();

        expect($post->fresh()->tags)->toBe([['label' => 'First'], ['label' => 'External']]);
    });

    it('accepts explicitly refreshed paths without accepting changes to other fields', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])->fillForm(['rating' => 8]);

        $post->update(['title' => 'Refreshed title', 'rating' => 4]);
        $page->call('refreshTitle')
            ->assertSchemaStateSet(['title' => 'Refreshed title', 'rating' => 8])
            ->call('save')
            ->assertSet('staleFormState.changedPaths', ['rating']);

        expect($post->fresh()->rating)->toBe(4);
    });

    it('halts a stale section save before the caller can report success', function (): void {
        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(new class extends EditPost
        {
            public function form(Schema $schema): Schema
            {
                return $schema->components([
                    Section::make('Details')
                        ->key('details')
                        ->schema([TextInput::make('title'), TextInput::make('rating')])
                        ->footerActions([
                            Action::make('saveSection')
                                ->schema([TextInput::make('note')->required()])
                                ->action(static function (Section $component, EditRecord $livewire): void {
                                    $livewire->saveFormComponentOnly($component);

                                    Notification::make()->success()->title('Section saved')->send();
                                }),
                        ]),
                ]);
            }
        }, ['record' => $post->getKey()])->fillForm(['title' => 'Draft', 'rating' => 7]);

        $post->update(['title' => 'External edit', 'rating' => 9]);

        $page->callAction(TestAction::make('saveSection')->schemaComponent('details', 'form'), data: ['note' => 'Keep this note'])
            ->assertActionMounted(TestAction::make('resolveFormConflicts')->schemaComponent('form-actions', 'content'))
            ->assertNotNotified()
            ->assertSchemaStateSet(['title' => 'Draft'], 'form');

        expect($post->fresh()->title)->toBe('External edit');

        $page->call('resolveFormConflict', 'title', 'draft')
            ->assertSet('staleFormState.conflictingPaths', ['rating'])
            ->call('resolveFormConflict', 'rating', 'current')
            ->assertActionMounted(TestAction::make('saveSection')->schemaComponent('details', 'form'))
            ->assertSchemaStateSet(['note' => 'Keep this note'])
            ->callMountedAction()
            ->assertNotified();

        expect($post->fresh()->title)->toBe('Draft')->and($post->fresh()->rating)->toBe(9);
    });

    it('compares formatted values without running save transformations on stale edits', function (): void {
        TextInput::configureUsing(static function (TextInput $component): void {
            if ($component->getName() !== 'rating') {
                return;
            }

            $component->formatStateUsing(static fn ($state): int => $state * 10)
                ->dehydrateStateUsing(static function ($state, Post $record): int {
                    $record->author->update(['name' => 'Save transformation ran']);

                    return $state / 10;
                });
        });

        $post = Post::factory()->create(['title' => 'Original', 'rating' => 2]);
        $page = livewire(EditPost::class, ['record' => $post->getKey()])
            ->assertSchemaStateSet(['rating' => 20])
            ->fillForm(['title' => 'Draft', 'rating' => 30]);

        $page->call('save')->assertSet('staleFormState', null);

        expect($post->fresh()->rating)->toBe(3)
            ->and($post->author->fresh()->name)->toBe('Save transformation ran');

        $post->author->update(['name' => 'Preserved author']);
        $post->update(['rating' => 5]);
        $page->fillForm(['rating' => 40])->call('save')
            ->assertSet('staleFormState.current.rating', 50)
            ->assertSet('staleFormState.draft.rating', 40)
            ->assertSchemaStateSet(['rating' => 40], 'form');

        expect($post->author->fresh()->name)->toBe('Preserved author');
    });

    it('rechecks authorization before exposing or accepting current values', function (string $method): void {
        $ticket = Ticket::factory()->create();
        $page = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

        app()->bind(TicketPolicy::class . '::update', static fn (): bool => false);

        $page->call($method)->assertForbidden();
    })->with(['save', 'resolveFormConflict', 'discardFormChanges']);

    it('rejects saving and recovery after leaving the mounted parent scope', function (string $method): void {
        $parent = User::factory()->create();
        $otherParent = User::factory()->create();
        $post = Post::factory()->create(['author_id' => $parent->getKey()]);
        $page = livewire(EditUserPost::class, ['record' => $post->getKey(), 'parentRecord' => $parent]);

        $post->update(['author_id' => $otherParent->getKey()]);

        $page->call($method)->assertNotFound();
    })->with(['save', 'resolveFormConflict', 'discardFormChanges']);

    it('can choose draft or current values, retry and discard conflicts in the browser', function (): void {
        Artisan::call('filament:assets');
        $post = Post::factory()->create(['title' => 'Original title']);
        $draftAuthor = User::factory()->create();
        $currentAuthor = User::factory()->create();
        $titleInput = 'input[id="form.title"]';
        $authorSelect = 'select[id="form.author_id"]';
        $saveButton = 'form[id="form"] button[type="submit"]';
        $comparison = '[data-testid="stale-form-comparison"]';
        $modal = "[role=\"dialog\"]:has({$comparison})";
        $draftPreview = '[data-testid="stale-form-preview-draft"] input';
        $currentPreview = '[data-testid="stale-form-preview-current"] input';

        $page = visit(PostResource::getUrl('edit', ['record' => $post]))
            ->fill($titleInput, 'My draft')
            ->select($authorSelect, (string) $draftAuthor->getKey());

        $darkPage = visit(PostResource::getUrl('edit', ['record' => $post]))
            ->inDarkMode()
            ->fill($titleInput, 'Dark mode draft')
            ->select($authorSelect, (string) $draftAuthor->getKey());

        Post::query()->whereKey($post)->update(['title' => 'First external edit', 'author_id' => $currentAuthor->getKey()]);

        $page->click($saveButton)
            ->assertPresent($modal)
            ->assertVisible($comparison)
            ->assertValue($draftPreview, 'My draft')
            ->assertValue($currentPreview, 'First external edit')
            ->assertDisabled($draftPreview)
            ->assertDisabled($currentPreview)
            ->assertValue('[data-testid="stale-form-preview-draft"] select', (string) $draftAuthor->getKey())
            ->assertValue('[data-testid="stale-form-preview-current"] select', (string) $currentAuthor->getKey())
            ->assertDisabled('[data-testid="stale-form-preview-current"] select')
            ->assertMissing('[data-testid="stale-form-original"]')
            ->assertMissing('[data-testid="stale-form-refresh"]')
            ->assertNoAccessibilityIssues()
            ->click('section:has(select) [data-testid="stale-form-use-current"]')
            ->assertValue($authorSelect, (string) $currentAuthor->getKey())
            ->assertValue($titleInput, 'My draft')
            ->assertMissing('[data-testid="stale-form-preview-current"] select')
            ->assertNoAccessibilityIssues();

        $darkPage->click($saveButton)->assertVisible($comparison)
            ->assertNoAccessibilityIssues()
            ->click('section:has(select) [data-testid="stale-form-use-current"]')
            ->assertNoAccessibilityIssues();

        expect($post->fresh()->title)->toBe('First external edit');

        $page->keys($modal, 'Escape')
            ->assertMissing($modal)
            ->assertValue($titleInput, 'My draft')
            ->click($saveButton)
            ->assertPresent($modal)
            ->assertVisible($comparison);

        $page->click('[data-testid="stale-form-use-draft"]')
            ->assertMissing($comparison)
            ->assertMissing($modal)
            ->fill($titleInput, 'My revised draft');

        Post::query()->whereKey($post)->update(['title' => 'Second external edit']);

        $page->click($saveButton)
            ->assertVisible($comparison)
            ->assertValue($currentPreview, 'Second external edit')
            ->assertValue($titleInput, 'My revised draft');

        expect($post->fresh()->title)->toBe('Second external edit');

        $page->click('[data-testid="stale-form-use-current"]')
            ->assertMissing($comparison)
            ->assertValue($titleInput, 'Second external edit')
            ->click($saveButton)
            ->assertSeeIn('nav[aria-label]', 'Second external edit')
            ->assertMissing($comparison);

        expect($post->fresh()->title)->toBe('Second external edit');

        $page->fill($titleInput, 'Draft to discard');
        Post::query()->whereKey($post)->update(['title' => 'Latest external edit']);

        $page->click($saveButton)
            ->assertVisible($comparison)
            ->click('[data-testid="stale-form-discard"]')
            ->assertMissing($comparison)
            ->assertValue($titleInput, 'Latest external edit');

        expect($post->fresh()->title)->toBe('Latest external edit');
    });
});

it('can validate input', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->fillForm([
            'title' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['title' => 'required']);
});

it('can delete', function (): void {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->getKey(),
    ])
        ->callAction(DeleteAction::class);

    assertSoftDeleted($post);
});

it('can refresh data', function (): void {
    $post = Post::factory()->create();

    $page = livewire(EditPost::class, [
        'record' => $post->getKey(),
    ]);

    $originalPostTitle = $post->title;

    $page->assertSchemaStateSet([
        'title' => $originalPostTitle,
    ]);

    $newPostTitle = Str::random();

    $post->title = $newPostTitle;
    $post->save();

    $page->assertSchemaStateSet([
        'title' => $originalPostTitle,
    ]);

    $page->call('refreshTitle');

    $page->assertSchemaStateSet([
        'title' => $newPostTitle,
    ]);
});

test('actions will not interfere with database transactions on an error', function (): void {
    $post = Post::factory()->create();

    $transactionLevel = DB::transactionLevel();

    try {
        livewire(EditPost::class, [
            'record' => $post->getKey(),
        ])
            ->callAction('randomize_title');
    } catch (Exception $exception) {
        // Inspect the transaction level after the action exception is handled.
    }

    expect(DB::transactionLevel())
        ->toBe($transactionLevel);
});

it('can render ticket messages page without a policy', function (): void {
    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertSuccessful();
});

it('can render page if the policy `viewAny()` returns `true`', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy `viewAny()` returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page without a policy if authorization is strict', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $message = TicketMessage::factory()
        ->create();

    $this->get(TicketMessageResource::getUrl('edit', ['record' => $message]))
        ->assertServerError();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);
});

it('does not render page if the policy `viewAny()` returns `false`', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('does not render page if the policy `viewAny()` returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::viewAny', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('can render page if the policy `update()` returns `true`', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();
});

it('can render page if the policy `update()` returns an allowed response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::allow());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertSuccessful();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy `update()` returns `false`', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('does not render page if the policy `update()` returns a denied response', function (): void {
    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::deny());

    $ticket = Ticket::factory()
        ->create();

    $this->get(TicketResource::getUrl('edit', ['record' => $ticket]))
        ->assertForbidden();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('re-authorizes `update()` on Livewire updates after the initial mount', function (): void {
    $ticket = Ticket::factory()
        ->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    $component = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
});

it('re-authorizes `viewAny()` on Livewire updates after the initial mount of an edit page', function (): void {
    $ticket = Ticket::factory()
        ->create();

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);

    $component = livewire(EditTicket::class, ['record' => $ticket->getKey()]);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => false);

    $component
        ->set('data.subject', 'foo')
        ->assertStatus(403);

    app()->bind(TicketPolicy::class . '::viewAny', fn (): bool => true);
});

it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false): void {
    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

    $ticket = Ticket::factory()
        ->create();

    if ($isSoftDeleted) {
        $ticket->delete();
    }

    livewire(EditTicket::class, ['record' => $ticket->getKey()])
        ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}($action);

    app()->bind(TicketPolicy::class . '::' . $policyMethod, fn (): bool => true);
})->with([
    'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
    'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
    'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
    'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
    'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true],
    'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true],
    'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false],
    'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false],
    'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true],
    'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true],
    'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false],
    'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false],
    'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true],
    'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true],
    'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true],
    'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true],
    'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true],
    'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true],
    'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true],
    'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true],
    'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true],
    'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true],
    'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false],
    'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false],
]);

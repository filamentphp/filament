<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\Exceptions\ActionNotResolvableException;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsReorderableTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Tables\PostsTableWithSessionPersistence;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns `tableArguments` from `getTableArguments()`', function (): void {
    $component = new TableSelectLivewireComponent;
    $component->tableArguments = ['filter' => 'active', 'sort' => 'name'];

    expect($component->getTableArguments())->toBe(['filter' => 'active', 'sort' => 'name']);
});

it('returns empty array for `getTableArguments()` by default', function (): void {
    $component = new TableSelectLivewireComponent;

    expect($component->getTableArguments())->toBe([]);
});

it('renders a blade string', function (): void {
    $component = new TableSelectLivewireComponent;

    expect($component->render())->toBe('{{ $this->table }}');
});

it('rejects temporary uploads without a file-upload field in its own schema', function (): void {
    livewire(TableSelectLivewireComponent::class, [
        'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
    ])
        ->call('_startUpload', 'componentFileAttachments.data.photo', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});

it('allows uploads only to its own mounted filter action schema', function (string $statePath): void {
    livewire(TableSelectLivewireComponent::class, [
        'tableConfiguration' => base64_encode(TableSelectUploadConfiguration::class),
    ])
        ->call('mountAction', 'upload', [], ['schemaComponent' => 'tableFiltersForm.attachment.query'])
        ->assertSet('mountedActions.0.name', 'upload')
        ->call('_startUpload', $statePath, [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');

    livewire(TableSelectLivewireComponent::class, [
        'tableConfiguration' => base64_encode(TableSelectUploadConfiguration::class),
    ])
        ->call('_startUpload', $statePath, [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
})->with([
    'file upload' => 'mountedActions.0.data.photo.fileKey',
    'attachment editor' => 'componentFileAttachments.mountedActions.0.data.content.fileKey',
]);

it('preserves native restoration for a scope-excluded `record`', function (): void {
    $post = Post::factory()->create();
    $post->delete();
    $component = livewire(TableSelectLivewireComponent::class, [
        'record' => $post,
        'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
    ])->assertSuccessful();
    $globalScopes = Post::getAllGlobalScopes();

    try {
        Post::addGlobalScope(
            'exclude-table-select-record',
            static fn (Builder $query): Builder => $query->whereKeyNot($post->getKey()),
        );

        $component
            ->set('tableSearch', 'search')
            ->assertSuccessful();

        expect($component->instance()->record->is($post))->toBeTrue()
            ->and($component->instance()->record->trashed())->toBeTrue();
    } finally {
        Post::setAllGlobalScopes($globalScopes);
    }
});

it('preserves an unsaved relationship parent on subsequent requests', function (): void {
    livewire(TableSelectLivewireComponent::class, [
        'model' => User::class,
        'record' => new User,
        'relationshipName' => 'posts',
        'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
    ])
        ->set('tableSearch', 'search')
        ->assertSuccessful();
});

describe('selection-only capabilities', function (): void {
    it('does not reorder persisted records even when reordering is requested directly', function (): void {
        $firstPost = Post::factory()->create(['sort' => 12]);
        $secondPost = Post::factory()->create(['sort' => 27]);

        $component = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(TableSelectCapabilityConfiguration::class),
        ])->call('reorderTable', [$secondPost->getKey(), $firstPost->getKey()]);

        expect([$firstPost->fresh()->sort, $secondPost->fresh()->sort])->toBe([12, 27]);

        $component->call('toggleTableReordering')
            ->call('moveTableRecord', (string) $firstPost->getKey(), 'down')
            ->assertDontSeeHtml('wire:click="toggleTableReordering"')
            ->assertDontSeeHtml('data-table-reorder-direction=');

        expect($component->instance()->isTableReordering())->toBeFalse()
            ->and($component->instance()->getTable()->isReorderable())->toBeFalse()
            ->and([$firstPost->fresh()->sort, $secondPost->fresh()->sort])->toBe([12, 27]);
    });

    it('cannot invoke callable column actions', function (): void {
        $post = Post::factory()->create(['content' => 'Original']);

        $component = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(TableSelectCapabilityConfiguration::class),
        ])->call('callTableColumnAction', 'title', (string) $post->getKey());

        expect($post->fresh()->content)->toBe('Original');

        $component->assertDontSeeHtml('callTableColumnAction(');
    });

    it('preserves closure-backed columns without executable actions', function (string $configuration): void {
        $post = Post::factory()->create(['title' => 'Selectable post', 'content' => 'Original']);

        $component = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode($configuration),
            'state' => [],
        ])->assertCanSeeTableRecords([$post])
            ->call('callTableColumnAction', 'title', (string) $post->getKey())
            ->call('mountAction', 'columnMutation', [], ['table' => true, 'recordKey' => (string) $post->getKey()])
            ->call('callMountedAction')
            ->assertSet('mountedActions', [])
            ->assertDontSeeHtml('callTableColumnAction(')
            ->assertDontSeeHtml('columnMutation');

        $table = $component->instance()->getTable();

        expect($post->fresh()->content)->toBe('Original')
            ->and(array_keys($table->getColumns()))->toBe(['title', 'content'])
            ->and($table->getAction('columnMutation'))->toBeNull();
    })->with([TableSelectStackConfiguration::class, TableSelectGroupConfiguration::class]);

    it('cannot resolve, mount, or call configured actions', function (string $name, bool $hasRecord, bool $isBulk): void {
        $post = Post::factory()->create(['content' => 'Original']);
        $context = ['table' => true, 'bulk' => $isBulk];

        if ($hasRecord) {
            $context['recordKey'] = (string) $post->getKey();
        }

        $component = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(TableSelectCapabilityConfiguration::class),
            'state' => [(string) $post->getKey()],
        ])->call('mountAction', $name, [], $context)
            ->call('callMountedAction');

        expect($post->fresh()->content)->toBe('Original');

        $component->assertSet('mountedActions', [])
            ->set('mountedActions', [['name' => $name, 'arguments' => [], 'context' => $context]])
            ->call('callMountedAction')
            ->assertSet('mountedActions', []);

        $table = $component->instance()->getTable();

        expect($post->fresh()->content)->toBe('Original')
            ->and($table->getAction($name))->toBeNull()
            ->and($table->getBulkAction($name))->toBeNull();

        expect(fn () => $component->instance()->getAction([['name' => $name, 'context' => $context]]))
            ->toThrow(ActionNotResolvableException::class);
    })->with([
        'column with record' => ['columnMutation', true, false],
        'column without record' => ['columnMutation', false, false],
        'empty state' => ['emptyMutation', false, false],
        'nested empty state group' => ['groupedEmptyMutation', false, false],
        'header' => ['headerMutation', false, false],
        'record' => ['recordMutation', true, false],
        'bulk' => ['bulkMutation', false, true],
    ]);

    it('preserves selection, labels, search, sort, filters, and pagination', function (): void {
        $alpha = Post::factory()->create(['title' => 'Alpha selection', 'is_published' => true]);
        $zulu = Post::factory()->create(['title' => 'Zulu selection', 'is_published' => true]);
        $draft = Post::factory()->create(['title' => 'Draft selection', 'is_published' => false]);

        $component = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(TableSelectCapabilityConfiguration::class),
            'state' => [],
        ])->assertCanSeeTableRecords([$alpha, $zulu, $draft])
            ->set('state', [(string) $zulu->getKey()])
            ->searchTable('selection')
            ->filterTable('published')
            ->sortTable('title', 'desc')
            ->assertCanSeeTableRecords([$zulu, $alpha], inOrder: true)
            ->assertCanNotSeeTableRecords([$draft])
            ->assertSet('state', [(string) $zulu->getKey()])
            ->set('tableRecordsPerPage', 1)
            ->assertCanSeeTableRecords([$zulu])
            ->assertCanNotSeeTableRecords([$alpha])
            ->call('setPage', 2)
            ->assertCanSeeTableRecords([$alpha])
            ->assertCanNotSeeTableRecords([$zulu])
            ->set('state', [])
            ->assertSet('state', []);

        expect($component->instance()->getTable()->getRecordTitle($alpha))->toBe('Alpha selection')
            ->and($component->instance()->getAllSelectableTableRecordKeys())->toEqualCanonicalizing([(string) $alpha->getKey(), (string) $zulu->getKey()]);

        $component->searchTable('no matches')
            ->assertCanNotSeeTableRecords([$alpha, $zulu, $draft])
            ->assertDontSee('Empty mutation')
            ->assertDontSee('Grouped empty mutation');
    });

    it('restores mutation capabilities on the ordinary owner when `readOnly()` becomes false', function (string $configuration): void {
        $firstPost = Post::factory()->create(['sort' => 12, 'content' => 'Original']);
        $secondPost = Post::factory()->create(['sort' => 27, 'content' => 'Original']);
        $component = livewire(TableSelectCapabilityOwner::class, ['configuration' => $configuration, 'isReadOnly' => true])
            ->assertDontSeeHtml('callTableColumnAction(')
            ->assertDontSeeHtml('columnMutation')
            ->set('isReadOnly', false)
            ->assertSeeHtml('callTableColumnAction(')
            ->assertSeeHtml('columnMutation')
            ->call('reorderTable', [$secondPost->getKey(), $firstPost->getKey()]);

        expect([$firstPost->fresh()->sort, $secondPost->fresh()->sort])->toBe([2, 1]);

        $component->call('callTableColumnAction', 'title', (string) $firstPost->getKey());

        expect($firstPost->fresh()->content)->toBe('Callable mutation');

        foreach (['columnMutation', 'emptyMutation', 'groupedEmptyMutation'] as $name) {
            $firstPost->update(['content' => 'Original']);
            $context = ['table' => true];

            if ($name === 'columnMutation') {
                $context['recordKey'] = (string) $firstPost->getKey();
            }

            $component->call('mountAction', $name, [], $context)
                ->assertSet('mountedActions.0.name', $name)
                ->call('callMountedAction');

            expect($firstPost->fresh()->content)->toBe('Action mutation');
        }

        expect($component->instance()->getTable()->isReorderable())->toBeTrue();
    })->with([TableSelectCapabilityConfiguration::class, TableSelectStackConfiguration::class, TableSelectGroupConfiguration::class]);
});

describe('session persistence', function (): void {
    it('disables session persistence on the table', function (): void {
        $livewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        $table = $livewire->getTable();

        expect($table->persistsFiltersInSession())->toBeFalse()
            ->and($table->persistsSearchInSession())->toBeFalse()
            ->and($table->persistsColumnSearchesInSession())->toBeFalse()
            ->and($table->persistsSortInSession())->toBeFalse()
            ->and($table->persistsGroupInSession())->toBeFalse()
            ->and($table->persistsColumnsInSession())->toBeFalse()
            ->and($table->persistsRecordsPerPageInSession())->toBeFalse();
    });

    it('keeps reordered columns in component state without writing to the session', function (): void {
        $testable = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ]);

        $reorderedTableColumns = array_reverse($testable->instance()->tableColumns);

        $testable
            ->call('applyTableColumnManager', $reorderedTableColumns, true)
            ->set('tableRecordsPerPage', 25)
            ->assertSet('tableColumns.0.name', 'author_id');

        $livewire = $testable->instance();

        expect(array_keys($livewire->getTable()->getColumns()))->toBe(['author_id', 'title'])
            ->and(session()->has($livewire->getTablePerPageSessionKey()))->toBeFalse()
            ->and(session()->has($livewire->getHasReorderedTableColumnsSessionKey()))->toBeFalse();

        $testable
            ->call('applyTableColumnManager', array_reverse($reorderedTableColumns), true)
            ->assertSet('tableColumns.0.name', 'title');

        expect(array_keys($testable->instance()->getTable()->getColumns()))->toBe(['title', 'author_id']);

        $freshLivewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        expect(array_keys($freshLivewire->getTable()->getColumns()))->toBe(['title', 'author_id']);
    });

    it('does not load table state from the session', function (): void {
        session()->put('filament.tables.' . hash('sha256', TableSelectLivewireComponent::class) . '.per_page', 25);
        session()->put('filament.tables.' . hash('sha256', TableSelectLivewireComponent::class) . '.columns', [
            [
                'type' => TableSelectLivewireComponent::TABLE_COLUMN_MANAGER_COLUMN_TYPE,
                'name' => 'title',
                'label' => 'Title',
                'isHidden' => false,
                'isToggled' => false,
                'isToggleable' => true,
                'isToggledHiddenByDefault' => false,
            ],
        ]);

        $livewire = livewire(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(PostsTableWithSessionPersistence::class),
        ])->instance();

        expect($livewire->tableRecordsPerPage)->toBe(10)
            ->and($livewire->isTableColumnToggledHidden('title'))->toBeFalse()
            ->and(session()->get($livewire->getTablePerPageSessionKey()))->toBe(25);
    });
});

class TableSelectCapabilityConfiguration
{
    public static function configure(Table $table): Table
    {
        $mutation = static fn () => Post::query()->update(['content' => 'Action mutation']);

        return $table
            ->query(Post::query())
            ->recordTitleAttribute('title')
            ->columns(static::columns())
            ->filters([Filter::make('published')->query(static fn (Builder $query): Builder => $query->where('is_published', true))])
            ->reorderable('sort')
            ->paginationPageOptions([1, 10])
            ->defaultPaginationPageOption(10)
            ->headerActions([Action::make('headerMutation')->requiresConfirmation()->action($mutation)])
            ->recordActions([Action::make('recordMutation')->requiresConfirmation()->action($mutation)])
            ->toolbarActions([BulkAction::make('bulkMutation')->requiresConfirmation()->action($mutation)])
            ->emptyStateActions([
                Action::make('emptyMutation')->requiresConfirmation()->action($mutation),
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('groupedEmptyMutation')->requiresConfirmation()->action($mutation),
                    ]),
                ]),
            ]);
    }

    protected static function columns(): array
    {
        return [
            TextColumn::make('title')
                ->searchable()
                ->sortable()
                ->action(static fn (Post $record) => $record->update(['content' => 'Callable mutation'])),
            TextColumn::make('content')
                ->action(Action::make('columnMutation')->requiresConfirmation()->action(static fn () => Post::query()->update(['content' => 'Action mutation']))),
        ];
    }
}

class TableSelectStackConfiguration extends TableSelectCapabilityConfiguration
{
    protected static function columns(): array
    {
        return [Stack::make(static fn (): array => parent::columns())->collapsible()];
    }
}

class TableSelectGroupConfiguration extends TableSelectCapabilityConfiguration
{
    protected static function columns(): array
    {
        return [ColumnGroup::make('Post', static fn (): array => parent::columns())];
    }
}

class TableSelectCapabilityOwner extends PostsReorderableTable
{
    public string $configuration = TableSelectCapabilityConfiguration::class;

    public bool $isReadOnly = false;

    public function table(Table $table): Table
    {
        return $this->configuration::configure($table->readOnly(fn (): bool => $this->isReadOnly));
    }
}

class TableSelectUploadConfiguration
{
    public static function configure(Table $table): Table
    {
        return PostsTableWithSessionPersistence::configure($table)
            ->filters([
                Filter::make('attachment')
                    ->schema([
                        TextInput::make('query')
                            ->suffixAction(
                                Action::make('upload')->schema([
                                    FileUpload::make('photo'),
                                    MarkdownEditor::make('content'),
                                ]),
                            ),
                    ]),
            ]);
    }
}

<?php

use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render text column', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('title');
});

it('can render text column with relationship', function () {
    Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanRenderTableColumn('author.name');
});

it('can sort records', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('title'), inOrder: true);
});

it('can sort records with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->sortTable('author.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.name'), inOrder: true)
        ->sortTable('author.name', 'desc')
        ->assertCanSeeTableRecords($posts->sortByDesc('author.name'), inOrder: true);
});

it('can search records', function () {
    $posts = Post::factory()->count(10)->create();

    $title = $posts->first()->title;

    livewire(PostsTable::class)
        ->searchTable($title)
        ->assertCanSeeTableRecords($posts->where('title', $title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $title));
});

it('can search individual column records', function () {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;

    livewire(PostsTable::class)
        ->searchTableColumns(['content' => $content])
        ->assertCanSeeTableRecords($posts->where('content', $content))
        ->assertCanNotSeeTableRecords($posts->where('content', '!=', $content));
});

it('can search posts with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author->name;

    livewire(PostsTable::class)
        ->searchTable($author)
        ->assertCanSeeTableRecords($posts->where('author.name', $author))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $author));
});

it('can search individual column records with relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns(['author.email' => $authorEmail])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can search multiple individual columns', function () {
    $posts = Post::factory()->count(10)->create();

    $content = $posts->first()->content;
    $authorEmail = $posts->first()->author->email;

    livewire(PostsTable::class)
        ->searchTableColumns([
            'content' => $content,
            'author.email' => $authorEmail,
        ])
        ->assertCanSeeTableRecords($posts->where('author.email', $authorEmail))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $authorEmail));
});

it('can hide a column', function () {
    livewire(PostsTable::class)
        ->assertTableColumnVisible('visible')
        ->assertTableColumnHidden('hidden');
});

it('can call a column action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableColumnAction('title', $post)
        ->assertDispatched('title-action-called');
});

it('can call a column action object', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('column-action-object', $post)
        ->assertDispatched('column-action-object-called');
});

describe('column action availability', function (): void {
    it('checks column visibility before mounting or calling an action object', function (bool $isHidden, bool $isGrouped, bool $hasModal): void {
        $post = Post::factory()->create();
        $component = livewire(ColumnActionsTable::class, compact('isHidden', 'isGrouped', 'hasModal'))
            ->call('mountTableAction', 'column-action', $post->getKey());

        if ($isHidden) {
            $component->assertSet('mountedTableActions', [])
                ->assertNotDispatched('open-modal')
                ->call('callMountedTableAction')
                ->assertNotDispatched('column-action-called');

            return;
        }

        if ($hasModal) {
            $component->assertTableActionMounted('column-action')
                ->assertDispatched('open-modal')
                ->assertNotDispatched('column-action-called')
                ->call('callMountedTableAction');
        }

        $component->assertDispatched('column-action-called', recordKey: $post->getKey());
    })->with([false, true])->with([false, true])->with([false, true]);

    it('uses the requested record for object and callable actions', function (bool $isCallable): void {
        $visiblePost = Post::factory()->create(['is_published' => true]);
        $hiddenPost = Post::factory()->create(['is_published' => false]);
        $component = livewire(ColumnActionsTable::class, ['isDynamic' => true]);

        foreach ([$hiddenPost, $visiblePost, $hiddenPost] as $post) {
            $component->call($isCallable ? 'callTableColumnAction' : 'mountTableAction', $isCallable ? 'content' : 'column-action', $post->getKey())
                ->assertSet('mountedTableActions', []);

            if ($post->is_published) {
                $component->assertDispatched('column-action-called', recordKey: $post->getKey());
            } else {
                $component->assertNotDispatched('column-action-called');
            }
        }
    })->with([false, true]);

    it('refuses hidden callable column actions', function (): void {
        $post = Post::factory()->create();

        livewire(ColumnActionsTable::class, ['isHidden' => true])
            ->call('callTableColumnAction', 'content', $post->getKey())
            ->assertNotDispatched('column-action-called');
    });

    it('refuses hidden column actions without a record key', function (): void {
        livewire(ColumnActionsTable::class, ['isHidden' => true, 'hasModal' => true])
            ->call('mountTableAction', 'column-action')
            ->assertSet('mountedTableActions', [])
            ->assertNotDispatched('open-modal');
    });

    it('rechecks column visibility for the current user before submitting a nested action', function (): void {
        $post = Post::factory()->create();
        $this->actingAs($post->author);

        $component = livewire(ColumnActionsTable::class, ['isUserDependent' => true, 'hasModal' => true])
            ->call('mountTableAction', 'column-action', $post->getKey())
            ->call('mountTableAction', 'nested')
            ->assertTableActionMounted('column-action.nested');

        $this->actingAs(User::factory()->create());

        $component->call('callMountedTableAction')
            ->assertSet('mountedTableActions', [])
            ->assertNotDispatched('column-action-called');
    });

    it('rechecks record-dependent column visibility before submitting an action', function (bool $isNested): void {
        $post = Post::factory()->create(['is_published' => true]);
        $component = livewire(ColumnActionsTable::class, ['isDynamic' => true, 'hasModal' => true])
            ->call('mountTableAction', 'column-action', $post->getKey())
            ->assertTableActionMounted('column-action');

        if ($isNested) {
            $component->call('mountTableAction', 'nested')
                ->assertTableActionMounted('column-action.nested');
        }

        $post->update(['is_published' => false]);

        $component->call('callMountedTableAction')
            ->assertSet('mountedTableActions', [])
            ->assertNotDispatched('column-action-called');
    })->with([false, true]);

    it('preserves authorization on visible column actions', function (): void {
        $post = Post::factory()->create();

        livewire(ColumnActionsTable::class, ['isAuthorized' => false, 'hasModal' => true])
            ->call('mountTableAction', 'column-action', $post->getKey())
            ->assertTableActionNotMounted('column-action')
            ->call('callMountedTableAction')
            ->assertNotDispatched('column-action-called');
    });

    it('keeps ordinary record actions separate from hidden columns with the same action name', function (): void {
        $post = Post::factory()->create();

        livewire(ColumnActionsTable::class, ['isHidden' => true, 'hasDuplicateRecordAction' => true])
            ->callTableAction('column-action', $post)
            ->assertDispatched('record-action-called', recordKey: $post->getKey())
            ->assertNotDispatched('column-action-called');
    });

    it('preserves last-registration precedence for duplicate column actions', function (bool $isHidden, bool $isActionReused): void {
        $post = Post::factory()->create();
        $component = livewire(ColumnActionsTable::class, ['isHidden' => ! $isHidden, 'hasDuplicateColumnAction' => true, 'isActionReused' => $isActionReused])
            ->call('mountTableAction', 'column-action', $post->getKey());

        if ($isHidden) {
            $component->assertSet('mountedTableActions', [])
                ->assertNotDispatched('column-action-called')
                ->assertNotDispatched('duplicate-column-action-called');
        } else {
            $component->assertDispatched($isActionReused ? 'column-action-called' : 'duplicate-column-action-called');
        }
    })->with([false, true])->with([false, true]);

    it('does not carry column ownership into reused or cloned actions on another table', function (bool $isCloned): void {
        $post = Post::factory()->create();
        $source = livewire(ColumnActionsTable::class, ['isHidden' => true])->instance();
        $action = $source->getTable()->getFlatActions()['column-action'];
        $calledRecordKeys = [];
        $action->action(function (Post $record) use (&$calledRecordKeys): void {
            $calledRecordKeys[] = $record->getKey();
        });
        $target = livewire(ColumnActionsTable::class)->instance();
        $target->getTable()->columns([
            TextColumn::make('title')->action($isCloned ? clone $action : $action),
        ]);
        $target->mountTableAction('column-action', $post->getKey());

        expect($calledRecordKeys)->toBe([$post->getKey()]);

        $target->getTable()->columns([])->actions([$isCloned ? clone $action : $action]);
        $target->mountTableAction('column-action', $post->getKey());

        expect($calledRecordKeys)->toBe([$post->getKey(), $post->getKey()]);
    })->with([false, true]);
});

it('can state whether a column has the correct value', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('with_state', 'correct state', $post)
        ->assertTableColumnStateNotSet('with_state', 'incorrect state', $post);
});

it('can state whether a column has the correct formatted value', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnFormattedStateSet('formatted_state', 'formatted state', $post)
        ->assertTableColumnFormattedStateNotSet('formatted_state', 'incorrect formatted state', $post);
});

it('can output values in a JSON array column of objects', function () {
    $post = Post::factory()->create([
        'json_array_of_objects' => [
            ['value' => 'foo'],
            ['value' => 'bar'],
            ['value' => 'baz'],
        ],
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnStateSet('json_array_of_objects.*.value', ['foo', 'bar', 'baz'], $post);
});

it('can state whether a column has extra attributes', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasExtraAttributes('extra_attributes', ['class' => 'text-danger-500'], $post)
        ->assertTableColumnDoesNotHaveExtraAttributes('extra_attributes', ['class' => 'text-primary-500'], $post);
});

it('can state whether a column has a description', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableColumnHasDescription('with_description', 'description below', $post)
        ->assertTableColumnHasDescription('with_description', 'description above', $post, 'above')
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description below', $post)
        ->assertTableColumnDoesNotHaveDescription('with_description', 'incorrect description above', $post, 'above');
});

it('can state whether a select column has options', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableSelectColumnHasOptions('with_options', ['red' => 'Red', 'blue' => 'Blue'], $post)
        ->assertTableSelectColumnDoesNotHaveOptions('with_options', ['one' => 'One', 'two' => 'Two'], $post);
});

it('can assert that a column exists with the given configuration', function () {
    $publishedPost = Post::factory()->create([
        'is_published' => true,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->isSortable() &&
                $column->isSearchable() &&
                $column->getPrefix() == 'published';
        }, $publishedPost);

    $unpublishedPost = Post::factory()->create([
        'is_published' => false,
    ]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('title2', function (TextColumn $column) {
            return $column->getPrefix() == 'unpublished';
        }, $unpublishedPost);

    $this->expectException('PHPUnit\Framework\ExpectationFailedException');
    $this->expectExceptionMessage('Failed asserting that a column with the name [title] and provided configuration exists on the [' . PostsTable::class . '] component');

    livewire(PostsTable::class)
        ->assertTableColumnExists('title', function (TextColumn $column) {
            return $column->isTime();
        }, $publishedPost);
});

it('can automatically detect boolean cast attribute in icon column', function () {
    $post = Post::factory()
        ->create(['is_published' => false]);

    livewire(PostsTable::class)
        ->assertTableColumnExists('is_published', function (IconColumn $column) {
            return $column->isBoolean();
        }, $post);
});

class ColumnActionsTable extends PostsTable
{
    public bool $isHidden = false;

    public bool $isGrouped = false;

    public bool $hasModal = false;

    public bool $isDynamic = false;

    public bool $isUserDependent = false;

    public bool $isAuthorized = true;

    public bool $hasDuplicateRecordAction = false;

    public bool $hasDuplicateColumnAction = false;

    public bool $isActionReused = false;

    public function table(Table $table): Table
    {
        $isHidden = fn (?Post $record): bool => $this->isHidden
            || ($this->isDynamic && ($record?->is_published === false))
            || ($this->isUserDependent && (auth()->id() !== $record?->author_id));
        $callback = fn (Post $record) => $this->dispatch('column-action-called', recordKey: $record->getKey());
        $action = Action::make('column-action')
            ->requiresConfirmation($this->hasModal)
            ->authorize($this->isAuthorized)
            ->extraModalFooterActions([
                Action::make('nested')->requiresConfirmation()->action($callback),
            ])
            ->action($callback);
        $columns = [
            TextColumn::make('title')->hidden($isHidden)->action($action),
            TextColumn::make('content')->hidden($isHidden)->action($callback),
        ];

        if ($this->hasDuplicateColumnAction) {
            $columns[] = TextColumn::make('rating')->hidden(! $this->isHidden)
                ->action($this->isActionReused ? $action : Action::make('column-action')->action(fn () => $this->dispatch('duplicate-column-action-called')));
        }

        return $table->query(Post::query())
            ->columns($this->isGrouped ? [ColumnGroup::make('Post', $columns)] : $columns)
            ->actions($this->hasDuplicateRecordAction ? [
                Action::make('column-action')->action(fn (Post $record) => $this->dispatch('record-action-called', recordKey: $record->getKey())),
            ] : []);
    }
}

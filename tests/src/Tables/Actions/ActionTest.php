<?php

use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tests\Models\HiddenTeamUser;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\Team;
use Filament\Tests\Models\User;
use Filament\Tests\Tables\Fixtures\HiddenPivotTeamsTable;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

describe('hidden pivot state', function (): void {
    it('does not automatically fill hidden pivot fields in record actions', function (string $action, bool $duplicates, bool $isTranslatable): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);
        $key = $duplicates ? $user->teams()->withPivot('id')->first()->pivot->id : $team->getKey();

        livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'duplicates' => $duplicates, 'isTranslatable' => $isTranslatable])
            ->mountTableAction($action, (string) $key)
            ->assertTableActionDataSet(['created_at' => null, 'pivot_created_at' => null, 'user_id' => null, 'pivot_user_id' => null, 'role' => 'editor']);
    })->with(['view', 'edit', 'replicate'])->with([false, true])->with([false, true]);

    it('saves explicitly submitted hidden pivot fields and visible pivot fields', function (bool $isTranslatable): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);

        livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'isTranslatable' => $isTranslatable])
            ->callTableAction('edit', $team, ['created_at' => '2002-03-04 05:06:07', 'role' => 'manager'])
            ->assertHasNoTableActionErrors();

        $pivot = $user->teams()->withTimestamps()->withPivot('role')->first()->pivot;
        expect($pivot->created_at->format('Y-m-d H:i:s'))->toBe('2002-03-04 05:06:07')
            ->and($pivot->role)->toBe('manager');
    })->with([false, true]);

    it('does not write a pivot when submitted state has no pivot keys', function (): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);
        $saves = 0;
        HiddenTeamUser::saving(static function () use (&$saves): void {
            $saves++;
        });

        livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'hasPivotFields' => false])
            ->callTableAction('edit', $team, ['name' => 'Renamed'])
            ->assertHasNoTableActionErrors();

        expect($saves)->toBe(0)
            ->and($team->fresh()->name)->toBe('Renamed')
            ->and($user->teams()->withTimestamps()->first()->pivot->created_at->format('Y-m-d H:i:s'))->toBe('2001-02-03 04:05:06');
    });

    it('does not write an absent hidden pivot field while saving visible pivot fields', function (): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);

        livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'hasHiddenPivotField' => false])
            ->callTableAction('edit', $team, ['role' => 'manager'])
            ->assertHasNoTableActionErrors();

        $pivot = $user->teams()->withTimestamps()->withPivot('role')->first()->pivot;
        expect($pivot->created_at->format('Y-m-d H:i:s'))->toBe('2001-02-03 04:05:06')
            ->and($pivot->role)->toBe('manager');
    });

    it('does not reintroduce translated hidden pivot attributes when filling an action', function (): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => '{"en":"Secret translation"}']);
        $pivot = new class extends HiddenTeamUser
        {
            use HasTranslations;

            public $translatable = ['role'];

            protected $hidden = ['role', 'created_at', 'user_id'];
        };

        Table::configureUsing(
            static fn (Table $table) => $table->relationship(static fn () => $user->teams()->using($pivot::class)->withPivot(['id', 'role'])->withTimestamps()),
            during: static fn () => livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'isTranslatable' => true])
                ->mountTableAction('edit', $team)
                ->assertTableActionDataSet(['role' => null, 'created_at' => null]),
        );
    });

    it('does not automatically fill hidden pivot attributes in infolists', function (bool $duplicates): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor', 'created_at' => '2001-02-03 04:05:06']);
        $key = $duplicates ? $user->teams()->withPivot('id')->first()->pivot->id : $team->getKey();
        $component = livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'duplicates' => $duplicates])->mountTableAction('info', (string) $key);
        $entries = $component->instance()->getMountedTableAction()->getInfolist()->getComponents();

        expect($entries[0]->getState())->toBeNull()
            ->and($entries[1]->getState())->toBeNull()
            ->and($entries[2]->getState())->toBeNull()
            ->and($entries[3]->getState())->toBe('editor');
    })->with([false, true]);

    it('does not automatically fill hidden pivot attributes in bulk actions', function (bool $duplicates): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role' => 'editor']);
        $key = $duplicates ? $user->teams()->withPivot('id')->first()->pivot->id : $team->getKey();

        livewire(HiddenPivotTeamsTable::class, ['userKey' => $user->getKey(), 'duplicates' => $duplicates])
            ->mountTableBulkAction('inspect', [(string) $key])
            ->assertTableBulkActionDataSet(['created_at' => null, 'pivot_created_at' => null, 'user_id' => null, 'pivot_user_id' => null, 'role' => 'editor']);
    })->with([false, true]);
});

describe('table action registration context', function (): void {
    it('does not mount non-record actions with a supplied record', function (string $name): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction($name, $post)
            ->assertTableActionNotMounted($name)
            ->callMountedTableAction()
            ->assertNotDispatched("{$name}-called");
    })->with(['arguments', 'groupedHeader', 'empty', 'create']);

    it('can call non-record actions without a supplied record', function (string $name): void {
        livewire(PostsTable::class)
            ->callTableAction($name)
            ->assertDispatched("{$name}-called");
    })->with(['arguments', 'groupedHeader', 'empty']);

    it('can mount and submit a column action with its record', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction('column-action-object', $post)
            ->assertTableActionMounted('column-action-object')
            ->callMountedTableAction()
            ->assertDispatched('column-action-object-called', recordKey: $post->getKey());
    });

    it('can submit a nested grouped record action', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->callTableAction('groupedWithVisibleGroupCondition.nested', $post)
            ->assertDispatched('nested-called', recordKey: $post->getKey());
    });

    it('does not submit a header action after its record is changed', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->mountTableAction('arguments')
            ->set('mountedTableActionRecord', $post->getKey())
            ->call('callMountedTableAction')
            ->assertNotDispatched('arguments-called');
    });

    it('only submits a nested header action without a supplied record', function (): void {
        $post = Post::factory()->create();

        livewire(PostsTable::class)
            ->call('mountTableAction', 'arguments', $post->getKey())
            ->call('mountTableAction', 'nested', $post->getKey())
            ->call('callMountedTableAction')
            ->assertNotDispatched('nested-called');

        livewire(PostsTable::class)
            ->callTableAction('arguments.nested')
            ->assertDispatched('nested-called');
    });

    it('can create another header record after correcting validation errors', function (): void {
        $post = Post::factory()->create();
        $originalCount = Post::count();

        $livewire = livewire(PostsTable::class)
            ->mountTableAction('create')
            ->callMountedTableAction()
            ->assertHasTableActionErrors(['title' => 'required', 'author_id' => 'required'])
            ->assertTableActionMounted('create')
            ->setTableActionData([
                'title' => $firstTitle = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'First metadata'],
            ])
            ->callMountedTableAction(['another' => true])
            ->assertHasNoTableActionErrors()
            ->assertTableActionMounted('create')
            ->assertTableActionDataSet(['title' => null, 'metadata' => ['seo_title' => null]]);

        $firstPost = Post::query()->where('title', $firstTitle)->firstOrFail();

        $livewire->assertDispatched('created', recordKey: $firstPost->getKey())
            ->setTableActionData([
                'title' => $secondTitle = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'Second metadata'],
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors()
            ->assertTableActionNotMounted('create');

        $secondPost = Post::query()->where('title', $secondTitle)->firstOrFail();

        $livewire->assertDispatched('created', recordKey: $secondPost->getKey());

        expect(Post::count())->toBe($originalCount + 2)
            ->and($firstPost->metadata?->seo_title)->toBe('First metadata')
            ->and($secondPost->metadata?->seo_title)->toBe('Second metadata');
    });

    it('does not save create action relationships to the supplied table record', function (): void {
        $post = Post::factory()->create();
        $metadata = $post->metadata()->create(['seo_title' => 'Original metadata']);

        livewire(PostsTable::class)
            ->callTableAction('createFromRecord', $post, data: [
                'title' => $title = Str::random(),
                'author_id' => $post->author_id,
                'metadata' => ['seo_title' => 'New table metadata'],
            ])
            ->assertHasNoTableActionErrors();

        expect($metadata->refresh()->seo_title)->toBe('Original metadata')
            ->and(Post::query()->where('title', $title)->firstOrFail()->metadata?->seo_title)->toBe('New table metadata');
    });
});

it('can call action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction(DeleteAction::class, $post);

    assertSoftDeleted($post);
});

it('can call an action with data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoTableActionErrors()
        ->assertDispatched('data-called', data: [
            'payload' => $payload,
        ]);
});

it('can call action inside group', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->callTableAction('groupedDelete', $post);

    assertSoftDeleted($post);
});

it('can validate an action\'s data', function () {
    livewire(PostsTable::class)
        ->callTableAction('data', data: [
            'payload' => null,
        ])
        ->assertHasTableActionErrors(['payload' => ['required']])
        ->assertNotDispatched('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PostsTable::class)
        ->mountTableAction('data')
        ->assertTableActionDataSet([
            'foo' => 'bar',
        ])
        ->assertTableActionDataSet(function (array $data): bool {
            return $data['foo'] === 'bar';
        });
});

it('can call an action with arguments', function () {
    livewire(PostsTable::class)
        ->callTableAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertDispatched('arguments-called', arguments: [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(PostsTable::class)
        ->callTableAction('halt')
        ->assertDispatched('halt-called')
        ->assertTableActionHalted('halt');
});

it('can hide an action', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableActionVisible('visible')
        ->assertTableActionHidden('hidden')
        ->assertTableActionVisible('groupedWithVisibleGroupCondition', $post)
        ->assertTableActionHidden('groupedWithHiddenGroupCondition', $post);
});

it('can disable an action', function () {
    livewire(PostsTable::class)
        ->assertTableActionEnabled('enabled')
        ->assertTableActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasIcon('hasIcon', 'heroicon-m-pencil-square')
        ->assertTableActionDoesNotHaveIcon('hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasLabel('hasLabel', 'My Action')
        ->assertTableActionDoesNotHaveLabel('hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasColor('hasColor', 'primary')
        ->assertTableActionDoesNotHaveColor('hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(PostsTable::class)
        ->assertTableActionHasUrl('url', 'https://filamentphp.com')
        ->assertTableActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(PostsTable::class)
        ->assertTableActionShouldOpenUrlInNewTab('urlInNewTab')
        ->assertTableActionShouldNotOpenUrlInNewTab('urlNotInNewTab');
});

it('can state whether a table action exists', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('exists')
        ->assertTableActionDoesNotExist('doesNotExist');
});

it('can state whether table actions exist in order', function () {
    livewire(PostsTable::class)
        ->assertTableActionsExistInOrder(['edit', 'delete'])
        ->assertTableHeaderActionsExistInOrder(['exists', 'existsInOrder'])
        ->assertTableEmptyStateActionsExistInOrder(['emptyExists', 'emptyExistsInOrder']);
});

it('can state whether a table action exists with a given configuration', function () {
    livewire(PostsTable::class)
        ->assertTableActionExists('attachMultiple', fn (AttachAction $action) => $action->isMultiple())
        ->assertTableActionDoesNotExist(AttachAction::class, fn (AttachAction $action) => $action->isMultiple());
});

it('only restores available table action chains and their forms', function (string $restriction, array $mountedActions): void {
    $component = livewire(RestoredTableActions::class, ['restriction' => $restriction])
        ->set('mountedTableActions', $mountedActions)
        ->call('$refresh');

    expect($component->instance()->getMountedTableAction() !== null)->toBe($restriction === '');

    if (($restriction === '') && (count($mountedActions) > 1)) {
        $component->call('callMountedTableAction')->assertDispatched('restored-table-child-called')
            ->assertSet('mountedTableActions', []);
    }

    if ($restriction !== '') {
        $component->assertDontSee('Table restoration secret')
            ->call('callMountedTableAction')->assertNotDispatched('restored-table-child-called')
            ->assertSet('mountedTableActions', []);

        expect($component->instance()->getForm('mountedTableActionForm'))->toBeNull();
    }
})->with(['', 'hidden', 'invisible', 'disabled', 'unauthorized'])->with([
    'root' => [['restricted']],
    'nested' => [['restricted', 'child']],
    'sparse nested' => [[1 => 'restricted', 2 => 'child']],
]);

it('preserves parent edits when mounting an unavailable table child', function (string $actionName): void {
    livewire(RestoredTableActions::class)
        ->mountTableAction('restricted')
        ->setTableActionData(['value' => 'edited value'])
        ->call('mountTableAction', $actionName)
        ->call('$refresh')
        ->assertSet('mountedTableActions', ['restricted'])
        ->assertTableActionDataSet(['value' => 'edited value']);
})->with(['missing', 'unavailable']);

class RestoredTableActions extends PostsTable
{
    public string $restriction = '';

    public function table(Table $table): Table
    {
        return parent::table($table)->headerActions([
            Action::make('restricted')
                ->modalDescription('Table restoration secret')
                ->hidden(fn (): bool => $this->restriction === 'hidden')
                ->visible(fn (): bool => $this->restriction !== 'invisible')
                ->disabled(fn (): bool => $this->restriction === 'disabled')
                ->authorize(fn (): bool => $this->restriction !== 'unauthorized')
                ->form([TextInput::make('value')])
                ->extraModalFooterActions([
                    Action::make('unavailable')->disabled(),
                    Action::make('child')
                        ->requiresConfirmation()
                        ->disabled(fn (): bool => $this->restriction === 'completed')
                        ->cancelParentActions()
                        ->action(function (): void {
                            $this->restriction = 'completed';
                            $this->dispatch('restored-table-child-called');
                        }),
                ]),
        ]);
    }
}

it('can replicate a record', function () {
    $post = Post::factory()->create();

    livewire(PostsTable::class)
        ->assertTableActionExists('replicate')
        ->callTableAction('replicate', $post)
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    assertDatabaseHas('posts', [
        'title' => $post->title . ' (Copy)',
    ]);
});

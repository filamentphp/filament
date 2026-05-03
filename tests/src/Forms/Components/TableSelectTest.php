<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Tables\PostsTable;
use Filament\Tests\Fixtures\Tables\TeamsTable;
use Filament\Tests\Fixtures\Tables\UsersTable;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can load state from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('teams'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(TableSelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) use ($teams) {
            expect(collect($state['teams'])->sort()->values()->all())
                ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('teams');
    expect($eagerUser->relationLoaded('teams'))->toBeTrue();

    DB::flushQueryLog();

    livewire(TableSelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) use ($teams) {
            expect(collect($state['teams'])->sort()->values()->all())
                ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
});

it('can load state from a `BelongsTo` relationship using eager loaded data without additional queries', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create(['team_id' => $team->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('team'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(TableSelectWithBelongsToRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet([
            'team_id' => (string) $team->id,
        ]);

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('team');
    expect($eagerUser->relationLoaded('team'))->toBeTrue();

    DB::flushQueryLog();

    livewire(TableSelectWithBelongsToRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet([
            'team_id' => (string) $team->id,
        ]);

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
});

it('can load state from a `HasMany` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('posts'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(TableSelectWithHasManyRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet(function (array $state) use ($posts) {
            expect(collect($state['posts'])->sort()->values()->all())
                ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('posts');
    expect($eagerUser->relationLoaded('posts'))->toBeTrue();

    DB::flushQueryLog();

    livewire(TableSelectWithHasManyRelationship::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) use ($posts) {
            expect(collect($state['posts'])->sort()->values()->all())
                ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");
});

it('can load state from a `BelongsToMany` relationship inside a Repeater using eager loaded data without additional queries', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $company = Company::factory()->create();
    $teams = Team::factory()->count(2)->create(['company_id' => $company->id]);
    $users = User::factory()->count(3)->create();

    foreach ($teams as $team) {
        $team->users()->attach($users);
    }

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(RepeaterWithTableSelectBelongsToManyRelationship::class, ['record' => $company->fresh()])
        ->assertSchemaStateSet(function (array $state) use ($users) {
            expect($state['teams'])->toHaveCount(2);
            foreach ($state['teams'] as $teamState) {
                expect(collect($teamState['users'])->sort()->values()->all())
                    ->toBe($users->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());
            }

            return [];
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    DB::flushQueryLog();

    livewire(RepeaterWithTableSelectBelongsToManyRelationshipEagerLoaded::class, ['record' => $company->fresh()])
        ->assertSchemaStateSet(function (array $state) use ($users) {
            expect($state['teams'])->toHaveCount(2);
            foreach ($state['teams'] as $teamState) {
                expect(collect($teamState['users'])->sort()->values()->all())
                    ->toBe($users->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());
            }

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(1, "Expected to save 1 query with eager loading, but saved {$queriesSaved}");

    $undoRepeaterFake();
});

describe('loading relationships', function (): void {
    it('returns early from `fillStateFromRelationship()` when state is already filled', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $otherTeam = Team::factory()->create();

        livewire(TableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (TableSelect $component) use ($otherTeam): bool {
                // Pre-fill the state, then call fillStateFromRelationship - it should not overwrite
                $component->state((string) $otherTeam->id);
                $component->fillStateFromRelationship();

                expect((string) $component->getState())->toBe((string) $otherTeam->id);

                return true;
            });
    });

    it('loads state from a `HasOne` relationship', function (): void {
        $user = User::factory()->create();
        $publishedPost = Post::factory()->create(['author_id' => $user->id, 'is_published' => true]);

        livewire(TableSelectWithHasOneRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'publishedPost' => (string) $publishedPost->id,
            ]);
    });

    it('loads state from a `HasManyThrough` relationship', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(TableSelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertSchemaStateSet(function (array $state) use ($posts): array {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });
    });

    it('loads state from a `BelongsToThrough` relationship', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(TableSelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'company' => (string) $company->id,
            ]);
    });
});

describe('saving relationships', function (): void {
    it('can save selected options to a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(TableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(3);
    });

    it('can detach removed options from a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        livewire(TableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(2);
    });

    it('can save with `pivotData()` using `syncWithPivotValues()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();

        livewire(TableSelectWithBelongsToManyRelationshipAndPivotData::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        $pivotRows = DB::table('team_user')
            ->where('user_id', $user->id)
            ->get();

        expect($pivotRows)->toHaveCount(2);
        expect($pivotRows->first()->role)->toBe('member');
    });

    it('associates a `BelongsTo` model when saving', function (): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        livewire(TableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->fillForm(['team_id' => (string) $team->id])
            ->assertFormComponentExists('team_id', function (TableSelect $component) use ($team): bool {
                $component->saveStateToRelationship();

                // associate() sets the foreign key on the in-memory model
                expect($component->getRecord()->team_id)->toBe($team->id);

                return true;
            });
    });

    it('does not overwrite a `BelongsTo` foreign key that was already set on a recently created record', function (): void {
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();

        $user = User::factory()->create(['team_id' => $teamA->id]);

        livewire(TableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (TableSelect $component) use ($teamA, $teamB): bool {
                // Mimic the post-create state where wasRecentlyCreated is true and the FK is already filled
                $component->getRecord()->wasRecentlyCreated = true;
                $component->getRecord()->team_id = $teamA->id;

                $component->state((string) $teamB->id);
                $component->saveStateToRelationship();

                // Security guard kicks in: pre-existing FK is preserved on the in-memory model
                expect($component->getRecord()->team_id)->toBe($teamA->id);

                return true;
            });

        // The persisted record is unchanged too
        expect($user->fresh()->team_id)->toBe($teamA->id);
    });

    it('nulls and re-associates foreign keys when saving a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $orphanPosts = Post::factory()->count(2)->create(['author_id' => null]);
        $existingPost = Post::factory()->create(['author_id' => $user->id]);

        livewire(TableSelectWithHasManyRelationship::class, ['record' => $user])
            ->fillForm(['posts' => $orphanPosts->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        // Existing post should be detached (author_id nulled) and orphan posts re-associated
        expect($existingPost->fresh()->author_id)->toBeNull();
        foreach ($orphanPosts as $post) {
            expect($post->fresh()->author_id)->toBe($user->id);
        }
    });

    it('nulls and re-associates a foreign key when saving a `HasOne` relationship', function (): void {
        $user = User::factory()->create();
        $existingPost = Post::factory()->create(['author_id' => $user->id, 'is_published' => true]);
        $orphanPost = Post::factory()->create(['author_id' => null, 'is_published' => true]);

        livewire(TableSelectWithHasOneRelationship::class, ['record' => $user])
            ->fillForm(['publishedPost' => (string) $orphanPost->id])
            ->call('save');

        expect($existingPost->fresh()->author_id)->toBeNull();
        expect($orphanPost->fresh()->author_id)->toBe($user->id);
    });

    it('treats saving a `HasManyThrough` relationship as a no-op', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        $loadedPostIds = $team->posts()->pluck('posts.id')->all();
        expect($loadedPostIds)->toHaveCount(2);

        livewire(TableSelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertFormComponentExists('posts', function (TableSelect $component) use ($posts): bool {
                $component->state([$posts->first()->id]);
                $component->saveStateToRelationship();

                return true;
            });

        // Posts and their author_id are unchanged - the early return prevents any mutation
        foreach ($posts as $post) {
            expect($post->fresh()->author_id)->toBe($user->id);
        }
    });

    it('treats saving a `BelongsToThrough` relationship as a no-op', function (): void {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $companyA->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        // Sanity check via znck/eloquent-belongs-to-through
        expect($user->company?->id)->toBe($companyA->id);

        livewire(TableSelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertFormComponentExists('company', function (TableSelect $component) use ($companyB): bool {
                $component->state((string) $companyB->id);
                $component->saveStateToRelationship();

                return true;
            });

        expect($user->fresh()->company?->id)->toBe($companyA->id);
    });
});

describe('properties', function (): void {
    it('defaults `isMultiple()` to `false`', function (): void {
        $select = TableSelect::make('team');

        expect($select->isMultiple())->toBeFalse();
    });

    it('can set `multiple()`', function (): void {
        $select = TableSelect::make('teams')->multiple();

        expect($select->isMultiple())->toBeTrue();
    });

    it('can set `multiple()` with a `Closure`', function (): void {
        $select = TableSelect::make('teams')
            ->multiple(static fn (): bool => true);

        expect($select->isMultiple())->toBeTrue();
    });

    it('throws `LogicException` for `getTableConfiguration()` when not set', function (): void {
        $select = TableSelect::make('team');

        $select->getTableConfiguration();
    })->throws(LogicException::class);

    it('can set `tableConfiguration()`', function (): void {
        $select = TableSelect::make('team')
            ->tableConfiguration(TeamsTable::class);

        expect($select->getTableConfiguration())->toBe(TeamsTable::class);
    });

    it('can set `tableConfiguration()` with a `Closure`', function (): void {
        $select = TableSelect::make('team')
            ->tableConfiguration(static fn (): string => TeamsTable::class);

        expect($select->getTableConfiguration())->toBe(TeamsTable::class);
    });

    it('returns empty array for `getTableArguments()` by default', function (): void {
        $select = TableSelect::make('team');

        expect($select->getTableArguments())->toBe([]);
    });

    it('can set `tableArguments()`', function (): void {
        $select = TableSelect::make('team')
            ->tableArguments(['filter' => 'active']);

        expect($select->getTableArguments())->toBe(['filter' => 'active']);
    });

    it('defaults `shouldIgnoreRelatedRecords()` to `false`', function (): void {
        $select = TableSelect::make('team');

        expect($select->shouldIgnoreRelatedRecords())->toBeFalse();
    });

    it('can set `ignoreRelatedRecords()`', function (): void {
        $select = TableSelect::make('team')->ignoreRelatedRecords();

        expect($select->shouldIgnoreRelatedRecords())->toBeTrue();
    });

    it('can set `ignoreRelatedRecords()` with a `Closure`', function (): void {
        $select = TableSelect::make('team')
            ->ignoreRelatedRecords(static fn (): bool => true);

        expect($select->shouldIgnoreRelatedRecords())->toBeTrue();
    });

    it('returns `false` for `hasRelationship()` by default', function (): void {
        $select = TableSelect::make('team');

        expect($select->hasRelationship())->toBeFalse();
    });

    it('returns `true` for `hasRelationship()` after `relationship()` is called', function (): void {
        $select = TableSelect::make('teams')
            ->relationship('teams');

        expect($select->hasRelationship())->toBeTrue();
    });
});

class TableSelectWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('teams')
                    ->relationship('teams')
                    ->tableConfiguration(TeamsTable::class)
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithBelongsToManyRelationshipAndPivotData extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('teams')
                    ->relationship('teams')
                    ->tableConfiguration(TeamsTable::class)
                    ->pivotData(['role' => 'member'])
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithBelongsToRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('team_id')
                    ->relationship('team')
                    ->tableConfiguration(TeamsTable::class),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithHasManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('posts')
                    ->relationship('posts')
                    ->tableConfiguration(PostsTable::class)
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithHasOneRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('publishedPost')
                    ->relationship('publishedPost')
                    ->tableConfiguration(PostsTable::class),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithHasManyThroughRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Team $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('posts')
                    ->relationship('posts', 'title')
                    ->tableConfiguration(PostsTable::class)
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class TableSelectWithBelongsToThroughRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TableSelect::make('company')
                    ->relationship('company', 'name')
                    ->tableConfiguration(\Filament\Tests\Fixtures\Tables\CompaniesTable::class),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithTableSelectBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Company $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('teams')
                    ->relationship('teams')
                    ->schema([
                        TextInput::make('name'),
                        TableSelect::make('users')
                            ->relationship('users')
                            ->tableConfiguration(UsersTable::class)
                            ->multiple(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithTableSelectBelongsToManyRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public Company $record;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('teams')
                    ->relationship(
                        'teams',
                        modifyQueryUsing: fn ($query) => $query->with('users'),
                    )
                    ->schema([
                        TextInput::make('name'),
                        TableSelect::make('users')
                            ->relationship('users')
                            ->tableConfiguration(UsersTable::class)
                            ->multiple(),
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

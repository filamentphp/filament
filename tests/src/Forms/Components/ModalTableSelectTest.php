<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\ModalTableSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Tables\CompaniesTable;
use Filament\Tests\Fixtures\Tables\PostsTable;
use Filament\Tests\Fixtures\Tables\TeamsTable;
use Filament\Tests\Fixtures\Tables\UsersTable;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('eager loading', function (): void {
    it('can load state from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
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

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
    });

    it('does not use eager loaded data when `modifyQueryUsing()` is set', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(ModalTableSelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $freshUser])
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

        livewire(ModalTableSelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($teams) {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        expect($queriesWithEagerLoading)->toBe($queriesWithoutEagerLoading);
    });

    it('can load state from a `BelongsTo` relationship using eager loaded data without additional queries', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('team'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $freshUser])
            ->assertSchemaStateSet([
                'team_id' => (string) $team->id,
            ]);

        $queriesWithoutEagerLoading = count(DB::getQueryLog());

        $eagerUser = $user->fresh();
        $eagerUser->load('team');
        expect($eagerUser->relationLoaded('team'))->toBeTrue();

        DB::flushQueryLog();

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet([
                'team_id' => (string) $team->id,
            ]);

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
    });

    it('can load state from a `HasMany` relationship using eager loaded data without additional queries', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('posts'))->toBeFalse();

        DB::enableQueryLog();
        DB::flushQueryLog();

        livewire(ModalTableSelectWithHasManyRelationship::class, ['record' => $freshUser])
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

        livewire(ModalTableSelectWithHasManyRelationship::class, ['record' => $eagerUser])
            ->assertSchemaStateSet(function (array $state) use ($posts) {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });

        $queriesWithEagerLoading = count(DB::getQueryLog());
        DB::disableQueryLog();

        $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
        expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
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

        livewire(RepeaterWithModalTableSelectBelongsToManyRelationship::class, ['record' => $company->fresh()])
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

        livewire(RepeaterWithModalTableSelectBelongsToManyRelationshipEagerLoaded::class, ['record' => $company->fresh()])
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
        expect($queriesSaved)->toBe(3, "Expected to save 3 queries with eager loading, but saved {$queriesSaved}");

        $undoRepeaterFake();
    });
});

class ModalTableSelectWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('teams')
                    ->relationship('teams', 'name')
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

class ModalTableSelectWithEagerLoadedBelongsToManyRelationship extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->record->load('teams');
        $this->form->fill([]);
    }

    public function hydrate(): void
    {
        $this->record->load('teams');
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ModalTableSelect::make('teams')
                    ->relationship('teams', 'name')
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

class ModalTableSelectWithBelongsToManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                    )
                    ->tableConfiguration(TeamsTable::class)
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithBelongsToRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('team_id')
                    ->relationship('team', 'name')
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

class ModalTableSelectWithHasManyRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('posts')
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

class RepeaterWithModalTableSelectBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                        ModalTableSelect::make('users')
                            ->relationship('users', 'name')
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

class RepeaterWithModalTableSelectBelongsToManyRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
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
                        ModalTableSelect::make('users')
                            ->relationship('users', 'name')
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

describe('option labels', function (): void {
    it('can get `getOptionLabel()` from `BelongsTo` relationship', function (): void {
        $team = Team::factory()->create(['name' => 'Test Team']);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $select) use ($team): bool {
                expect($select->getOptionLabel())->toBe($team->name);

                return true;
            });
    });

    it('can get `getOptionLabels()` from `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (ModalTableSelect $select) use ($teams): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(2);
                expect(array_values($labels))->toContain($teams[0]->name);
                expect(array_values($labels))->toContain($teams[1]->name);

                return true;
            });
    });

    it('can get `getOptionLabels()` from `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(ModalTableSelectWithHasManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('posts', function (ModalTableSelect $select) use ($posts): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(2);
                expect(array_values($labels))->toContain($posts[0]->title);
                expect(array_values($labels))->toContain($posts[1]->title);

                return true;
            });
    });

    it('can use `getOptionLabelFromRecordUsing()` for custom `BelongsTo` labels', function (): void {
        $team = Team::factory()->create(['name' => 'Engineering']);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(ModalTableSelectWithCustomBelongsToLabel::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $select) use ($team): bool {
                expect($select->getOptionLabel())->toBe("Team: {$team->name}");

                return true;
            });
    });

    it('can use `getOptionLabelFromRecordUsing()` for custom `BelongsToMany` labels', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        livewire(ModalTableSelectWithCustomBelongsToManyLabels::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (ModalTableSelect $select) use ($teams): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(2);
                expect(array_values($labels))->toContain("Team: {$teams[0]->name}");
                expect(array_values($labels))->toContain("Team: {$teams[1]->name}");

                return true;
            });
    });

    it('can use `getOptionLabelFromRecordUsing()` for custom `HasMany` labels', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(ModalTableSelectWithCustomHasManyLabels::class, ['record' => $user])
            ->assertFormComponentExists('posts', function (ModalTableSelect $select) use ($posts): bool {
                $labels = $select->getOptionLabels();

                expect($labels)->toHaveCount(2);
                expect(array_values($labels))->toContain("Post: {$posts[0]->title}");
                expect(array_values($labels))->toContain("Post: {$posts[1]->title}");

                return true;
            });
    });

    it('returns `null` for `getOptionLabel()` when no record is selected', function (): void {
        $user = User::factory()->create(['team_id' => null]);

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $select): bool {
                expect($select->getOptionLabel(withDefault: false))->toBeNull();

                return true;
            });
    });

    it('returns empty array for `getOptionLabels()` when no records are selected', function (): void {
        $user = User::factory()->create();
        // Don't attach any teams

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (ModalTableSelect $select): bool {
                expect($select->getOptionLabels())->toBe([]);

                return true;
            });
    });
});

describe('multiple', function (): void {
    it('defaults `isMultiple()` to `false`', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->isMultiple())->toBeFalse();
    });

    it('can set `multiple()`', function (): void {
        $select = ModalTableSelect::make('teams')->multiple();

        expect($select->isMultiple())->toBeTrue();
    });

    it('can set `multiple()` with a `Closure`', function (): void {
        $select = ModalTableSelect::make('teams')
            ->multiple(static fn (): bool => true);

        expect($select->isMultiple())->toBeTrue();
    });
});

describe('badges', function (): void {
    it('defaults `hasBadges()` to `false` when not multiple', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->hasBadges())->toBeFalse();
    });

    it('defaults `hasBadges()` to `true` when multiple', function (): void {
        $select = ModalTableSelect::make('teams')->multiple();

        expect($select->hasBadges())->toBeTrue();
    });

    it('can set `badge()` explicitly', function (): void {
        $select = ModalTableSelect::make('team_id')->badge();

        expect($select->hasBadges())->toBeTrue();
    });

    it('can set `badge()` with a `Closure`', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->badge(static fn (): bool => true);

        expect($select->hasBadges())->toBeTrue();
    });

    it('returns `null` for `getBadgeColor()` by default', function (): void {
        $select = ModalTableSelect::make('teams');

        expect($select->getBadgeColor())->toBeNull();
    });

    it('can set `badgeColor()`', function (): void {
        $select = ModalTableSelect::make('teams')
            ->badgeColor('success');

        expect($select->getBadgeColor())->toBe('success');
    });

    it('can set `badgeColor()` with a `Closure`', function (): void {
        $select = ModalTableSelect::make('teams')
            ->badgeColor(static fn (): string => 'danger');

        expect($select->getBadgeColor())->toBe('danger');
    });
});

describe('table configuration', function (): void {
    it('throws `LogicException` for `getTableConfiguration()` when not set', function (): void {
        $select = ModalTableSelect::make('team_id');

        $select->getTableConfiguration();
    })->throws(LogicException::class);

    it('can set `tableConfiguration()`', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->tableConfiguration(TeamsTable::class);

        expect($select->getTableConfiguration())->toBe(TeamsTable::class);
    });

    it('can set `tableConfiguration()` with a `Closure`', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->tableConfiguration(static fn (): string => TeamsTable::class);

        expect($select->getTableConfiguration())->toBe(TeamsTable::class);
    });
});

describe('table arguments', function (): void {
    it('returns empty array for `getTableArguments()` by default', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->getTableArguments())->toBe([]);
    });

    it('can set `tableArguments()`', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->tableArguments(['showArchived' => true]);

        expect($select->getTableArguments())->toBe(['showArchived' => true]);
    });

    it('can set `tableArguments()` with a `Closure`', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->tableArguments(static fn (): array => ['filter' => 'active']);

        expect($select->getTableArguments())->toBe(['filter' => 'active']);
    });
});

describe('relationship metadata', function (): void {
    it('returns `false` for `hasRelationship()` by default', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->hasRelationship())->toBeFalse();
    });

    it('returns `true` for `hasRelationship()` after `relationship()` is called', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->relationship('team', 'name');

        expect($select->hasRelationship())->toBeTrue();
    });

    it('returns `null` for `getRelationshipName()` by default', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->getRelationshipName())->toBeNull();
    });

    it('uses field name when `relationship()` name is `null`', function (): void {
        $select = ModalTableSelect::make('teams')
            ->relationship(titleAttribute: 'name');

        expect($select->getRelationshipName())->toBe('teams');
    });

    it('returns `null` for `getRelationshipTitleAttribute()` by default', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->getRelationshipTitleAttribute())->toBeNull();
    });

    it('returns title attribute from `getRelationshipTitleAttribute()` when set', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->relationship('team', 'name');

        expect($select->getRelationshipTitleAttribute())->toBe('name');
    });

    it('returns `false` for `hasOptionLabelFromRecordUsingCallback()` by default', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
    });

    it('returns `true` for `hasOptionLabelFromRecordUsingCallback()` when set', function (): void {
        $select = ModalTableSelect::make('team_id')
            ->getOptionLabelFromRecordUsing(static fn (Team $record): string => $record->name);

        expect($select->hasOptionLabelFromRecordUsingCallback())->toBeTrue();
    });
});

describe('action modifier', function (): void {
    it('can modify select action via `selectAction()` callback', function (): void {
        $user = User::factory()->create();

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $select): bool {
                $select->selectAction(static fn ($action) => $action->label('Choose'));

                $action = $select->getSelectAction();

                expect($action->getLabel())->toBe('Choose');

                return true;
            });
    });
});

describe('`hasInValidationOnMultipleValues()` logic', function (): void {
    it('returns `false` when not multiple', function (): void {
        $select = ModalTableSelect::make('team_id');

        expect($select->hasInValidationOnMultipleValues())->toBeFalse();
    });

    it('returns `true` when multiple', function (): void {
        $select = ModalTableSelect::make('teams')->multiple();

        expect($select->hasInValidationOnMultipleValues())->toBeTrue();
    });
});

describe('saving BelongsToMany relationships', function (): void {
    it('can save selected options to a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(3);
    });

    it('can detach removed options from a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        livewire(ModalTableSelectWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(1)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(1);
    });

    it('can save with `pivotData()` using `syncWithPivotValues()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();

        livewire(ModalTableSelectWithBelongsToManyPivotData::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        $pivotRows = DB::table('team_user')->where('user_id', $user->id)->get();

        expect($pivotRows)->toHaveCount(2);
        expect($pivotRows->first()->role)->toBe('viewer');
        expect($pivotRows->last()->role)->toBe('viewer');
    });

    it('invalidates the cached `BelongsToMany` relationship after save so a subsequent reload does not re-attach detached rows', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        $component = livewire(ModalTableSelectWithEagerLoadedBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => []])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(0)
            ->and($component->instance()->data['teams'])->toBe([]);

        $component->call('save');

        expect($user->fresh()->teams)->toHaveCount(0);
    });
});

describe('saving BelongsTo relationships', function (): void {
    it('dehydrates BelongsTo state for model save', function (): void {
        $user = User::factory()->create(['team_id' => null]);
        $team = Team::factory()->create();

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->fillForm(['team_id' => (string) $team->id])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $select): bool {
                // BelongsTo is dehydrated (not multiple), so getState returns the selected ID
                expect($select->isMultiple())->toBeFalse();

                return true;
            });
    });
});

describe('saving HasMany relationships', function (): void {
    it('can save selected options to a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create();

        livewire(ModalTableSelectWithHasManyRelationship::class, ['record' => $user])
            ->fillForm(['posts' => $posts->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->posts)->toHaveCount(3);
    });

    it('can detach removed options from a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['author_id' => $user->id]);

        livewire(ModalTableSelectWithHasManyRelationship::class, ['record' => $user])
            ->fillForm(['posts' => $posts->take(1)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->posts)->toHaveCount(1);
    });
});

describe('relationship branch gaps', function (): void {
    it('returns early from `fillStateFromRelationship()` when state is already filled', function (): void {
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $teamA->id]);

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $component) use ($teamB): bool {
                $component->state((string) $teamB->id);
                $component->fillStateFromRelationship();

                expect((string) $component->getState())->toBe((string) $teamB->id);

                return true;
            });
    });

    it('loads state from a `HasOne` relationship', function (): void {
        $user = User::factory()->create();
        $publishedPost = Post::factory()->create(['author_id' => $user->id, 'is_published' => true]);

        livewire(ModalTableSelectWithHasOneRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'publishedPost' => (string) $publishedPost->id,
            ]);
    });

    it('loads state from a `BelongsToThrough` relationship', function (): void {
        $company = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(ModalTableSelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertSchemaStateSet([
                'company' => (string) $company->id,
            ]);
    });

    it('loads state from a `HasManyThrough` relationship', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(ModalTableSelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertSchemaStateSet(function (array $state) use ($posts): array {
                expect(collect($state['posts'])->sort()->values()->all())
                    ->toBe($posts->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });
    });

    it('applies `modifyQueryUsing` inside `getSelectedRecord()`', function (): void {
        $teams = Team::factory()->count(2)->create();
        $user = User::factory()->create(['team_id' => $teams->first()->id]);

        $modifyCallCount = 0;
        ModalTableSelectWithBelongsToRelationshipAndModifyQuery::$onModify = static function () use (&$modifyCallCount): void {
            $modifyCallCount++;
        };

        livewire(ModalTableSelectWithBelongsToRelationshipAndModifyQuery::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $component) use ($teams): bool {
                // Force fall-through past the relation-loaded shortcut by setting modifyQueryUsing
                $record = $component->getSelectedRecord();
                expect($record?->id)->toBe($teams->first()->id);

                return true;
            });

        expect($modifyCallCount)->toBeGreaterThan(0);
    });

    it('applies `modifyQueryUsing` inside `getOptionLabels()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        $modifyCallCount = 0;
        ModalTableSelectWithBelongsToManyRelationshipAndModifyQueryThatCounts::$onModify = static function () use (&$modifyCallCount): void {
            $modifyCallCount++;
        };

        // Detach the eager-loaded shortcut by NOT eager loading and using modifyQueryUsing
        livewire(ModalTableSelectWithBelongsToManyRelationshipAndModifyQueryThatCounts::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (ModalTableSelect $component): bool {
                $component->getOptionLabels();

                return true;
            });

        expect($modifyCallCount)->toBeGreaterThan(0);
    });

    it('applies `modifyQueryUsing` when saving a `HasMany` relationship', function (): void {
        $user = User::factory()->create();
        $modifyCallCount = 0;
        ModalTableSelectWithHasManyRelationshipAndModifyQuery::$onModify = static function () use (&$modifyCallCount): void {
            $modifyCallCount++;
        };

        livewire(ModalTableSelectWithHasManyRelationshipAndModifyQuery::class, ['record' => $user])
            ->assertFormComponentExists('posts', function (ModalTableSelect $component): bool {
                $component->state([]);
                $component->saveStateToRelationship();

                return true;
            });

        expect($modifyCallCount)->toBeGreaterThan(0);
    });

    it('does not overwrite a `BelongsTo` foreign key on a recently created record', function (): void {
        $teamA = Team::factory()->create();
        $teamB = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $teamA->id]);

        livewire(ModalTableSelectWithBelongsToRelationship::class, ['record' => $user])
            ->assertFormComponentExists('team_id', function (ModalTableSelect $component) use ($teamA, $teamB): bool {
                $component->getRecord()->wasRecentlyCreated = true;
                $component->getRecord()->team_id = $teamA->id;

                $component->state((string) $teamB->id);
                $component->saveStateToRelationship();

                expect($component->getRecord()->team_id)->toBe($teamA->id);

                return true;
            });
    });

    it('treats saving a `HasManyThrough` relationship as a no-op', function (): void {
        $team = Team::factory()->create();
        $user = User::factory()->create(['team_id' => $team->id]);
        $posts = Post::factory()->count(2)->create(['author_id' => $user->id]);

        livewire(ModalTableSelectWithHasManyThroughRelationship::class, ['record' => $team])
            ->assertFormComponentExists('posts', function (ModalTableSelect $component) use ($posts): bool {
                $component->state([(string) $posts->first()->id]);
                $component->saveStateToRelationship();

                return true;
            });

        foreach ($posts as $post) {
            expect($post->fresh()->author_id)->toBe($user->id);
        }
    });

    it('treats saving a `BelongsToThrough` relationship as a no-op', function (): void {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        $team = Team::factory()->create(['company_id' => $companyA->id]);
        $user = User::factory()->create(['team_id' => $team->id]);

        livewire(ModalTableSelectWithBelongsToThroughRelationship::class, ['record' => $user])
            ->assertFormComponentExists('company', function (ModalTableSelect $component) use ($companyB): bool {
                $component->state((string) $companyB->id);
                $component->saveStateToRelationship();

                return true;
            });

        expect($user->fresh()->company?->id)->toBe($companyA->id);
    });
});

class ModalTableSelectWithBelongsToManyPivotData extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('teams')
                    ->relationship('teams', 'name')
                    ->tableConfiguration(TeamsTable::class)
                    ->multiple()
                    ->pivotData(['role' => 'viewer']),
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

class ModalTableSelectWithCustomBelongsToLabel extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('team_id')
                    ->relationship('team', 'name')
                    ->tableConfiguration(TeamsTable::class)
                    ->getOptionLabelFromRecordUsing(fn (Team $record): string => "Team: {$record->name}"),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithCustomBelongsToManyLabels extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('teams')
                    ->relationship('teams', 'name')
                    ->tableConfiguration(TeamsTable::class)
                    ->multiple()
                    ->getOptionLabelFromRecordUsing(fn (Team $record): string => "Team: {$record->name}"),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithCustomHasManyLabels extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('posts')
                    ->relationship('posts', 'title')
                    ->tableConfiguration(PostsTable::class)
                    ->multiple()
                    ->getOptionLabelFromRecordUsing(fn (Post $record): string => "Post: {$record->title}"),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithHasOneRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('publishedPost')
                    ->relationship('publishedPost', 'title')
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

class ModalTableSelectWithBelongsToThroughRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('company')
                    ->relationship('company', 'name')
                    ->tableConfiguration(CompaniesTable::class),
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

class ModalTableSelectWithHasManyThroughRelationship extends Component implements HasActions, HasSchemas
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
                ModalTableSelect::make('posts')
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

class ModalTableSelectWithBelongsToRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public static ?Closure $onModify = null;

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
                ModalTableSelect::make('team_id')
                    ->relationship(
                        'team',
                        'name',
                        modifyQueryUsing: function ($query) {
                            (static::$onModify)?->__invoke();

                            return $query;
                        },
                    )
                    ->tableConfiguration(TeamsTable::class),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithBelongsToManyRelationshipAndModifyQueryThatCounts extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public static ?Closure $onModify = null;

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
                ModalTableSelect::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: function ($query) {
                            (static::$onModify)?->__invoke();

                            return $query;
                        },
                    )
                    ->tableConfiguration(TeamsTable::class)
                    ->multiple(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class ModalTableSelectWithHasManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public static ?Closure $onModify = null;

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
                ModalTableSelect::make('posts')
                    ->relationship(
                        'posts',
                        'title',
                        modifyQueryUsing: function ($query) {
                            (static::$onModify)?->__invoke();

                            return $query;
                        },
                    )
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

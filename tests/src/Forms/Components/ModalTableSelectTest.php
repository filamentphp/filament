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

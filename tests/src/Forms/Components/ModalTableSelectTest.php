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

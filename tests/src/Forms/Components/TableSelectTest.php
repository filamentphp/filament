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

<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can automatically validate valid options', function (): void {
    livewire(TestComponentWithSelect::class)
        ->fillForm(['number' => 'one'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(TestComponentWithSelect::class)
        ->fillForm(['number' => 'four'])
        ->call('save')
        ->assertHasFormErrors(['number' => ['in']]);
});

it('can automatically validate valid multiple options', function (): void {
    livewire(TestComponentWithMultipleSelect::class)
        ->fillForm(['number' => ['one', 'two']])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(TestComponentWithMultipleSelect::class)
        ->fillForm(['number' => ['one', 'four']])
        ->call('save')
        ->assertHasFormErrors(['number.1' => ['in']]);
});

it('can automatically validate valid options with custom search results', function (): void {
    livewire(TestComponentWithSelectCustomSearchResults::class)
        ->fillForm(['number' => 'one'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(TestComponentWithSelectCustomSearchResults::class)
        ->fillForm(['number' => 'four'])
        ->call('save')
        ->assertHasFormErrors(['number' => ['in']]);
});

it('can automatically validate valid multiple options with custom search results', function (): void {
    livewire(TestComponentWithMultipleSelectCustomSearchResults::class)
        ->fillForm(['number' => ['one', 'two']])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(TestComponentWithMultipleSelectCustomSearchResults::class)
        ->fillForm(['number' => ['one', 'four']])
        ->call('save')
        ->assertHasFormErrors(['number.1' => ['in']]);
});

it('can use `BelongsToMany` relationship as multiple select', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);

    expect($user->teams)->toHaveCount(3);

    livewire(TestComponentWithBelongsToManyMultipleSelect::class, ['record' => $user])
        ->assertSchemaStateSet([
            'teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all(),
        ]);
});

it('can save `BelongsToMany` relationship as multiple select', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams->first());

    expect($user->teams)->toHaveCount(1);

    $newTeamIds = $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all();

    livewire(TestComponentWithBelongsToManyMultipleSelect::class, ['record' => $user])
        ->fillForm(['teams' => $newTeamIds])
        ->call('save');

    $user->refresh();
    expect($user->teams)->toHaveCount(2);
    expect($user->teams->pluck('id')->sort()->values()->all())->toBe($teams->take(2)->pluck('id')->sort()->values()->all());
});

it('can use `BelongsToMany` relationship as single select', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);

    expect($user->teams)->toHaveCount(3);

    livewire(TestComponentWithBelongsToManySelect::class, ['record' => $user])
        ->assertSchemaStateSet([
            'teams' => (string) $teams->first()->id,
        ]);
});

it('can save `BelongsToMany` relationship as single select', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams->take(2));

    expect($user->teams)->toHaveCount(2);

    $newTeamId = (string) $teams->last()->id;

    livewire(TestComponentWithBelongsToManySelect::class, ['record' => $user])
        ->fillForm(['teams' => $newTeamId])
        ->call('save');

    $user->refresh();
    expect($user->teams)->toHaveCount(1);
    expect($user->teams->first()->id)->toBe($teams->last()->id);
});

it('can load state from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('teams'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(SelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
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

    livewire(SelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
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

    livewire(SelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $freshUser])
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

    livewire(SelectWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $eagerUser])
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

    livewire(SelectWithBelongsToRelationship::class, ['record' => $freshUser])
        ->assertSchemaStateSet([
            'team_id' => (string) $team->id,
        ]);

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('team');
    expect($eagerUser->relationLoaded('team'))->toBeTrue();

    DB::flushQueryLog();

    livewire(SelectWithBelongsToRelationship::class, ['record' => $eagerUser])
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

    livewire(SelectWithHasManyRelationship::class, ['record' => $freshUser])
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

    livewire(SelectWithHasManyRelationship::class, ['record' => $eagerUser])
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

    livewire(RepeaterWithSelectBelongsToManyRelationship::class, ['record' => $company->fresh()])
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

    livewire(RepeaterWithSelectBelongsToManyRelationshipEagerLoaded::class, ['record' => $company->fresh()])
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

it('can get option labels from a `BelongsToMany` relationship using eager loaded data without additional queries', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);
    $teamIds = $teams->pluck('id')->sort()->values()->all();

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('teams'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(SelectWithBelongsToManyRelationship::class, ['record' => $freshUser])
        ->assertFormComponentExists('teams', function (Select $select) use ($teamIds): bool {
            $labels = $select->getOptionLabels();

            expect($labels)->toHaveCount(3);
            expect(collect(array_keys($labels))->sort()->values()->all())->toBe($teamIds);

            return true;
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('teams');
    expect($eagerUser->relationLoaded('teams'))->toBeTrue();

    DB::flushQueryLog();

    livewire(SelectWithBelongsToManyRelationship::class, ['record' => $eagerUser])
        ->assertFormComponentExists('teams', function (Select $select) use ($teamIds): bool {
            $labels = $select->getOptionLabels();

            expect($labels)->toHaveCount(3);
            expect(collect(array_keys($labels))->sort()->values()->all())->toBe($teamIds);

            return true;
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(3, "Expected to save 3 queries with eager loading, but saved {$queriesSaved}");
});

it('can get option label from a `BelongsTo` relationship using eager loaded data without additional queries', function (): void {
    $team = Team::factory()->create();
    $user = User::factory()->create(['team_id' => $team->id]);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('team'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(SelectWithBelongsToRelationship::class, ['record' => $freshUser])
        ->assertFormComponentExists('team_id', function (Select $select) use ($team): bool {
            $label = $select->getOptionLabel();

            expect($label)->toBe($team->name);

            return true;
        });

    $queriesWithoutEagerLoading = count(DB::getQueryLog());

    $eagerUser = $user->fresh();
    $eagerUser->load('team');
    expect($eagerUser->relationLoaded('team'))->toBeTrue();

    DB::flushQueryLog();

    livewire(SelectWithBelongsToRelationship::class, ['record' => $eagerUser])
        ->assertFormComponentExists('team_id', function (Select $select) use ($team): bool {
            $label = $select->getOptionLabel();

            expect($label)->toBe($team->name);

            return true;
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    $queriesSaved = $queriesWithoutEagerLoading - $queriesWithEagerLoading;
    expect($queriesSaved)->toBe(2, "Expected to save 2 queries with eager loading, but saved {$queriesSaved}");
});

class TestComponentWithSelect extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleSelect extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])
                    ->multiple(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithSelectCustomSearchResults extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->getSearchResultsUsing(fn (string $search) => collect([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])->filter(fn (string $label, string $value): bool => str_contains($label, $search) || str_contains($value, $search)))
                    ->getOptionLabelUsing(fn (string $value): ?string => match ($value) {
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                        default => null,
                    }),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithMultipleSelectCustomSearchResults extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('number')
                    ->getSearchResultsUsing(fn (string $search) => collect([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])->filter(fn (string $label, string $value): bool => str_contains($label, $search) || str_contains($value, $search)))
                    ->getOptionLabelsUsing(function (array $values): array {
                        $labels = [];

                        foreach ($values as $value) {
                            $labels[$value] = match ($value) {
                                'one' => 'One',
                                'two' => 'Two',
                                'three' => 'Three',
                                default => null,
                            };
                        }

                        return $labels;
                    })
                    ->multiple(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithBelongsToManySelect extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->preload(),
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

class TestComponentWithBelongsToManyMultipleSelect extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public User $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
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

class SelectWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
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
                Select::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                    )
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithBelongsToRelationship extends Component implements HasActions, HasSchemas
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
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SelectWithHasManyRelationship extends Component implements HasActions, HasSchemas
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
                Select::make('posts')
                    ->relationship('posts', 'title')
                    ->multiple()
                    ->preload(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithSelectBelongsToRelationship extends Component implements HasActions, HasSchemas
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
                Repeater::make('posts')
                    ->relationship('posts')
                    ->schema([
                        TextInput::make('title'),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->preload(),
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

class RepeaterWithSelectBelongsToRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
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
                Repeater::make('posts')
                    ->relationship(
                        'posts',
                        modifyQueryUsing: fn ($query) => $query->with('author'),
                    )
                    ->schema([
                        TextInput::make('title'),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->preload(),
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

class RepeaterWithSelectBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                        Select::make('users')
                            ->relationship('users', 'name')
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

class RepeaterWithSelectBelongsToManyRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
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
                        Select::make('users')
                            ->relationship('users', 'name')
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

it('can select an option from a `native(false)` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Select Test')
        ->assertSee('Single Select')
        ->assertDontSee('One')
        ->assertDontSee('Two')
        ->click('[data-testid="single-select"] .fi-select-input-btn')
        ->waitForText('One')
        ->assertSee('Two')
        ->click('Two')
        ->assertDontSee('One')
        ->assertSee('Two')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/select-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('can select multiple options from a `multiple()` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Multiple Select')
        ->assertDontSee('Apple')
        ->assertDontSee('Cherry')
        ->click('[data-testid="multiple-select"] .fi-select-input-btn')
        ->waitForText('Apple')
        ->click('Apple')
        ->click('Cherry')
        ->keys('[data-testid="multiple-select"] .fi-select-input-btn', 'Escape')
        ->assertSee('Apple')
        ->assertSee('Cherry')
        ->assertNoSmoke();
});

it('can navigate options using keyboard in a `native(false)` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Single Select')
        ->assertDontSee('Two')
        ->click('[data-testid="single-select"] .fi-select-input-btn')
        ->waitForText('One')
        ->keys('[data-testid="single-select"] .fi-select-input-option.fi-selected', ['ArrowDown', 'Enter'])
        ->assertDontSee('One')
        ->assertSee('Two')
        ->assertNoSmoke();
});

it('can search and select an option in a `searchable()` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Searchable Select')
        ->assertDontSee('Purple')
        ->click('[data-testid="searchable-select"] .fi-select-input-btn')
        ->waitForText('Red')
        ->assertSee('Purple')
        ->type('[data-testid="searchable-select"] .fi-select-input-search-ctn input', 'pur')
        ->waitForText('Purple')
        ->assertDontSee('Red')
        ->click('Purple')
        ->assertSee('Purple')
        ->assertNoSmoke();
});

it('can clear a selected value in a `native(false)` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Clearable Select')
        ->assertDontSee('Active')
        ->click('[data-testid="clearable-select"] .fi-select-input-btn')
        ->waitForText('Active')
        ->click('Active')
        ->assertSee('Active')
        ->click('[data-testid="clearable-select"] .fi-select-input-value-remove-btn')
        ->assertDontSee('Active')
        ->assertNoSmoke();
});

it('can remove individual items from a `multiple()` select dropdown in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Multiple Select')
        ->assertDontSee('Apple')
        ->assertDontSee('Banana')
        ->click('[data-testid="multiple-select"] .fi-select-input-btn')
        ->waitForText('Apple')
        ->click('Apple')
        ->click('Banana')
        ->keys('[data-testid="multiple-select"] .fi-select-input-btn', 'Escape')
        ->assertSee('Apple')
        ->assertSee('Banana')
        ->click('[data-testid="multiple-select"] [aria-label="Remove Apple"]')
        ->assertDontSee('Apple')
        ->assertSee('Banana')
        ->assertNoSmoke();
});

it('shows "no options" message when dynamic options returns empty array', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Dynamic Empty Options')
        ->click('[data-testid="dynamic-empty-options-select"] .fi-select-input-btn')
        ->waitForText('No options available')
        ->assertSee('No options available')
        ->assertDontSee('Loading')
        ->assertNoSmoke();
});

it('shows options when dynamic options returns options', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Dynamic With Options')
        ->click('[data-testid="dynamic-with-options-select"] .fi-select-input-btn')
        ->waitForText('Option 1')
        ->assertSee('Option 1')
        ->assertSee('Option 2')
        ->assertDontSee('No options available')
        ->assertDontSee('Loading')
        ->assertNoSmoke();
});

it('shows "no options" message when dynamic options and search returns empty array', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Dynamic Options And Search Empty')
        ->click('[data-testid="dynamic-options-and-search-empty-select"] .fi-select-input-btn')
        ->waitForText('No options available')
        ->assertSee('No options available')
        ->assertDontSee('Loading')
        ->assertNoSmoke();
});

it('shows "no options" message when static options is empty array', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Static Empty Options')
        ->click('[data-testid="static-empty-options-select"] .fi-select-input-btn')
        ->waitForText('No options available')
        ->assertSee('No options available')
        ->assertNoSmoke();
});

it('shows options when dynamic options returns non-empty array', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/select-test')
        ->assertSee('Dynamic Options With Results')
        ->click('[data-testid="dynamic-options-with-results-select"] .fi-select-input-btn')
        ->waitForText('Dynamic Option 1')
        ->assertSee('Dynamic Option 1')
        ->assertSee('Dynamic Option 2')
        ->assertDontSee('No options available')
        ->assertDontSee('Loading')
        ->assertNoSmoke();
});

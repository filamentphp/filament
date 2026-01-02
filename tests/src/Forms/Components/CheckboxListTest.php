<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
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

    livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $freshUser])
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

    livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $eagerUser])
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

it('does not use eager loaded data when `modifyQueryUsing()` is set', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();
    $user->teams()->attach($teams);

    $freshUser = $user->fresh();
    expect($freshUser->relationLoaded('teams'))->toBeFalse();

    DB::enableQueryLog();
    DB::flushQueryLog();

    livewire(CheckboxListWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $freshUser])
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

    livewire(CheckboxListWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $eagerUser])
        ->assertSchemaStateSet(function (array $state) use ($teams) {
            expect(collect($state['teams'])->sort()->values()->all())
                ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

            return [];
        });

    $queriesWithEagerLoading = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queriesWithEagerLoading)->toBe($queriesWithoutEagerLoading);
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

    livewire(RepeaterWithCheckboxListBelongsToManyRelationship::class, ['record' => $company->fresh()])
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

    livewire(RepeaterWithCheckboxListBelongsToManyRelationshipEagerLoaded::class, ['record' => $company->fresh()])
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

class CheckboxListWithBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('teams')
                    ->relationship('teams', 'name'),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class CheckboxListWithBelongsToManyRelationshipAndModifyQuery extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name'),
                    ),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RepeaterWithCheckboxListBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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
                        CheckboxList::make('users')
                            ->relationship('users', 'name'),
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

class RepeaterWithCheckboxListBelongsToManyRelationshipEagerLoaded extends Component implements HasActions, HasSchemas
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
                        CheckboxList::make('users')
                            ->relationship('users', 'name'),
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

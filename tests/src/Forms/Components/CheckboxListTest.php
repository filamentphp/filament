<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
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

it('can get `getOptions()` from static array', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options([
            'option1' => 'Option 1',
            'option2' => 'Option 2',
        ]);

    expect($checkboxList->getOptions())->toBe([
        'option1' => 'Option 1',
        'option2' => 'Option 2',
    ]);
});

it('can get `getOptions()` from closure', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(fn (): array => [
            'option1' => 'Option 1',
            'option2' => 'Option 2',
        ]);

    expect($checkboxList->getOptions())->toBe([
        'option1' => 'Option 1',
        'option2' => 'Option 2',
    ]);
});

it('can get `getOptions()` from enum class string', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(StringBackedEnum::class);

    $options = $checkboxList->getOptions();

    expect($options)->toBe([
        'one' => 'One',
        'two' => 'Two',
        'three' => 'Three',
    ]);
});

it('can check `isBulkToggleable()` returns correct value', function (): void {
    $nonBulkToggleable = CheckboxList::make('options')
        ->options(['a' => 'A']);

    $bulkToggleable = CheckboxList::make('options')
        ->options(['a' => 'A'])
        ->bulkToggleable();

    expect($nonBulkToggleable->isBulkToggleable())->toBeFalse();
    expect($bulkToggleable->isBulkToggleable())->toBeTrue();
});

it('can get `getRelationshipName()` when relationship is set', function (): void {
    $withoutRelationship = CheckboxList::make('teams')
        ->options(['a' => 'A']);

    $withRelationship = CheckboxList::make('teams')
        ->relationship('teams', 'name');

    expect($withoutRelationship->getRelationshipName())->toBeNull();
    expect($withRelationship->getRelationshipName())->toBe('teams');
});

it('can get `getRelationshipTitleAttribute()`', function (): void {
    $checkboxList = CheckboxList::make('teams')
        ->relationship('teams', 'name');

    expect($checkboxList->getRelationshipTitleAttribute())->toBe('name');
});

it('can check `hasOptionLabelFromRecordUsingCallback()` returns correct value', function (): void {
    $withoutCallback = CheckboxList::make('teams')
        ->relationship('teams', 'name');

    $withCallback = CheckboxList::make('teams')
        ->relationship('teams', 'name')
        ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name);

    expect($withoutCallback->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
    expect($withCallback->hasOptionLabelFromRecordUsingCallback())->toBeTrue();
});

it('can check `hasOptionDescriptionFromRecordUsingCallback()` returns correct value', function (): void {
    $withoutCallback = CheckboxList::make('teams')
        ->relationship('teams', 'name');

    $withCallback = CheckboxList::make('teams')
        ->relationship('teams', 'name')
        ->getOptionDescriptionFromRecordUsing(fn ($record): string => $record->description ?? '');

    expect($withoutCallback->hasOptionDescriptionFromRecordUsingCallback())->toBeFalse();
    expect($withCallback->hasOptionDescriptionFromRecordUsingCallback())->toBeTrue();
});

it('can check `isSearchable()` returns correct value', function (): void {
    $nonSearchable = CheckboxList::make('options')
        ->options(['a' => 'A']);

    $searchable = CheckboxList::make('options')
        ->options(['a' => 'A'])
        ->searchable();

    expect($nonSearchable->isSearchable())->toBeFalse();
    expect($searchable->isSearchable())->toBeTrue();
});

it('can get enabled options using `getEnabledOptions()`', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'archived' => 'Archived',
        ])
        ->disableOptionWhen(fn (string $value): bool => $value === 'archived');

    expect($checkboxList->getEnabledOptions())->toBe([
        'active' => 'Active',
        'inactive' => 'Inactive',
    ]);
});

it('can get `getOptions()` from `BelongsToMany` relationship', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();

    livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $user])
        ->assertFormComponentExists('teams', function (CheckboxList $component) use ($teams): bool {
            $options = $component->getOptions();

            expect($options)->toHaveCount(3);
            expect(array_values($options))->toContain($teams[0]->name);
            expect(array_values($options))->toContain($teams[1]->name);
            expect(array_values($options))->toContain($teams[2]->name);

            return true;
        });
});

it('can filter relationship options with `modifyQueryUsing`', function (): void {
    $user = User::factory()->create();
    Team::factory()->create(['name' => 'Alpha Team']);
    Team::factory()->create(['name' => 'Beta Team']);
    Team::factory()->create(['name' => 'Gamma Team']);

    livewire(CheckboxListWithBelongsToManyRelationshipAndModifyQuery::class, ['record' => $user])
        ->assertFormComponentExists('teams', function (CheckboxList $component): bool {
            $options = $component->getOptions();

            expect($options)->toHaveCount(3);
            $optionValues = array_values($options);
            expect($optionValues[0])->toBe('Alpha Team');
            expect($optionValues[1])->toBe('Beta Team');
            expect($optionValues[2])->toBe('Gamma Team');

            return true;
        });
});

it('can use custom option labels from relationship via `getOptionLabelFromRecordUsing()`', function (): void {
    $user = User::factory()->create();
    Team::factory()->create(['name' => 'Alpha', 'description' => 'First team']);
    Team::factory()->create(['name' => 'Beta', 'description' => 'Second team']);

    livewire(CheckboxListWithCustomRelationshipLabel::class, ['record' => $user])
        ->assertFormComponentExists('teams', function (CheckboxList $component): bool {
            $options = $component->getOptions();

            expect($options)->toHaveCount(2);
            expect(array_values($options))->toContain('Alpha - First team');
            expect(array_values($options))->toContain('Beta - Second team');

            return true;
        });
});

it('can automatically validate valid options', function (): void {
    livewire(CheckboxListWithStaticOptionsValidation::class)
        ->fillForm(['options' => ['one', 'two']])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(CheckboxListWithStaticOptionsValidation::class)
        ->fillForm(['options' => ['one', 'four']])
        ->call('save')
        ->assertHasFormErrors(['options.1' => ['in']]);
});

it('can automatically validate valid options with `BelongsToMany` relationship', function (): void {
    $user = User::factory()->create();
    $teams = Team::factory()->count(3)->create();

    livewire(CheckboxListWithBelongsToManyRelationshipValidation::class, ['record' => $user])
        ->fillForm(['teams' => $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all()])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(CheckboxListWithBelongsToManyRelationshipValidation::class, ['record' => $user])
        ->fillForm(['teams' => [(string) $teams->first()->id, '99999']])
        ->call('save')
        ->assertHasFormErrors(['teams.1' => ['in']]);
});

it('rejects disabled static options during validation', function (): void {
    livewire(CheckboxListWithDisabledOptions::class)
        ->fillForm(['statuses' => ['active', 'pending']])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(CheckboxListWithDisabledOptions::class)
        ->fillForm(['statuses' => ['active', 'archived']])
        ->call('save')
        ->assertHasFormErrors(['statuses.1' => ['in']]);
});

it('passes validation when state is blank', function (): void {
    livewire(CheckboxListWithStaticOptionsValidation::class)
        ->fillForm(['options' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(CheckboxListWithStaticOptionsValidation::class)
        ->fillForm(['options' => []])
        ->call('save')
        ->assertHasNoFormErrors();
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

class CheckboxListWithCustomRelationshipLabel extends Component implements HasActions, HasSchemas
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
                    ->relationship('teams', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Team $record): string => "{$record->name} - {$record->description}"),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class CheckboxListWithStaticOptionsValidation extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CheckboxList::make('options')
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

    public function render(): View
    {
        return view('livewire.form');
    }
}

class CheckboxListWithBelongsToManyRelationshipValidation extends Component implements HasActions, HasSchemas
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

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class CheckboxListWithDisabledOptions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CheckboxList::make('statuses')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'archived' => 'Archived',
                    ])
                    ->disableOptionWhen(fn (string $value): bool => $value === 'archived'),
            ])
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

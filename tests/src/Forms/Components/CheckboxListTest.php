<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\GridDirection;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\Fixtures\Models\Company;
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

describe('eager loading', function (): void {
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
});

describe('options', function (): void {
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

});

describe('properties', function (): void {
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

});

describe('relationship options', function (): void {
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

});

describe('validation', function (): void {
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
});

describe('action names', function (): void {
    it('can return correct action names', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getSelectAllActionName())->toBe('selectAll');
        expect($checkboxList->getDeselectAllActionName())->toBe('deselectAll');
    });
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

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class CheckboxListWithEagerLoadedBelongsToManyRelationship extends Component implements HasActions, HasSchemas
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

class CheckboxListWithBelongsToManyRelationshipAndModifyQueryThatCounts extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('teams')
                    ->relationship(
                        'teams',
                        'name',
                        modifyQueryUsing: function ($query) {
                            (static::$onModify)?->__invoke();

                            return $query;
                        },
                    ),
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

it('can set `bulkToggleable()` with a `Closure`', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(['a' => 'A'])
        ->bulkToggleable(static fn (): bool => true);

    expect($checkboxList->isBulkToggleable())->toBeTrue();
});

it('can modify select all action via `selectAllAction()` callback', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(['a' => 'A'])
        ->bulkToggleable()
        ->selectAllAction(static fn ($action) => $action->label('Check All'));

    $action = $checkboxList->getSelectAllAction();

    expect($action->getLabel())->toBe('Check All');
});

it('can modify deselect all action via `deselectAllAction()` callback', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(['a' => 'A'])
        ->bulkToggleable()
        ->deselectAllAction(static fn ($action) => $action->label('Uncheck All'));

    $action = $checkboxList->getDeselectAllAction();

    expect($action->getLabel())->toBe('Uncheck All');
});

it('returns `true` for `hasInValidationOnMultipleValues()`', function (): void {
    $checkboxList = CheckboxList::make('options')
        ->options(['a' => 'A']);

    expect($checkboxList->hasInValidationOnMultipleValues())->toBeTrue();
});

it('returns fluent `$this` from `selectAllAction()`', function (): void {
    $checkboxList = CheckboxList::make('options');

    $result = $checkboxList->selectAllAction(static fn ($action) => $action);

    expect($result)->toBe($checkboxList);
});

describe('HTML allowing', function (): void {
    it('defaults `isHtmlAllowed()` to `false`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->isHtmlAllowed())->toBeFalse();
    });

    it('can set `allowHtml()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => '<b>Bold</b>'])
            ->allowHtml();

        expect($checkboxList->isHtmlAllowed())->toBeTrue();
    });

    it('can set `allowHtml()` with a `Closure`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->allowHtml(static fn (): bool => true);

        expect($checkboxList->isHtmlAllowed())->toBeTrue();
    });
});

describe('descriptions', function (): void {
    it('can set `descriptions()` and retrieve with `getDescription()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options([
                'a' => 'Option A',
                'b' => 'Option B',
            ])
            ->descriptions([
                'a' => 'Description for A',
                'b' => 'Description for B',
            ]);

        expect($checkboxList->getDescription('a'))->toBe('Description for A');
        expect($checkboxList->getDescription('b'))->toBe('Description for B');
    });

    it('returns `null` from `getDescription()` for missing key', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->descriptions(['a' => 'Desc']);

        expect($checkboxList->getDescription('missing'))->toBeNull();
    });

    it('returns correct value from `hasDescription()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A', 'b' => 'B'])
            ->descriptions(['a' => 'Has desc']);

        expect($checkboxList->hasDescription('a'))->toBeTrue();
        expect($checkboxList->hasDescription('b'))->toBeFalse();
    });

    it('can set `descriptions()` with a `Closure`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->descriptions(static fn (): array => ['a' => 'Dynamic desc']);

        expect($checkboxList->getDescription('a'))->toBe('Dynamic desc');
    });
});

describe('grid direction', function (): void {
    it('returns `null` for `getGridDirection()` by default', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getGridDirection())->toBeNull();
    });

    it('can set `gridDirection()` with a `GridDirection` enum', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->gridDirection(GridDirection::Column);

        expect($checkboxList->getGridDirection())->toBe(GridDirection::Column);
    });

    it('converts string to `GridDirection` enum in `getGridDirection()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->gridDirection('column');

        expect($checkboxList->getGridDirection())->toBe(GridDirection::Column);
    });

    it('can set `gridDirection()` with a `Closure`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->gridDirection(static fn (): string => 'column');

        expect($checkboxList->getGridDirection())->toBe(GridDirection::Column);
    });
});

describe('pivot data', function (): void {
    it('returns empty array for `getPivotData()` by default', function (): void {
        $checkboxList = CheckboxList::make('teams')
            ->relationship('teams', 'name');

        expect($checkboxList->getPivotData())->toBe([]);
    });

    it('can set `pivotData()`', function (): void {
        $checkboxList = CheckboxList::make('teams')
            ->relationship('teams', 'name')
            ->pivotData(['is_active' => true]);

        expect($checkboxList->getPivotData())->toBe(['is_active' => true]);
    });

    it('can set `pivotData()` with a `Closure`', function (): void {
        $checkboxList = CheckboxList::make('teams')
            ->relationship('teams', 'name')
            ->pivotData(static fn (): array => ['role' => 'member']);

        expect($checkboxList->getPivotData())->toBe(['role' => 'member']);
    });
});

describe('search configuration', function (): void {
    it('has `getSearchDebounce()` of `0` (set in `setUp()`)', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getSearchDebounce())->toBe(0);
    });

    it('can set `searchDebounce()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchDebounce(500);

        expect($checkboxList->getSearchDebounce())->toBe(500);
    });

    it('can set `searchable()` with a `Closure`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchable(static fn (): bool => true);

        expect($checkboxList->isSearchable())->toBeTrue();
    });

    it('returns default translation for `getNoSearchResultsMessage()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getNoSearchResultsMessage())->toBeString();
        expect($checkboxList->getNoSearchResultsMessage())->not->toBeEmpty();
    });

    it('can set `noSearchResultsMessage()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->noSearchResultsMessage('Nothing found');

        expect($checkboxList->getNoSearchResultsMessage())->toBe('Nothing found');
    });

    it('returns default translation for `getSearchPrompt()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getSearchPrompt())->toBeString();
        expect($checkboxList->getSearchPrompt())->not->toBeEmpty();
    });

    it('can set `searchPrompt()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchPrompt('Type to search...');

        expect($checkboxList->getSearchPrompt())->toBe('Type to search...');
    });

    it('defaults `shouldSearchLabels()` to `true`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->shouldSearchLabels())->toBeTrue();
    });

    it('can set `searchLabels()` to `false`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchLabels(false);

        expect($checkboxList->shouldSearchLabels())->toBeFalse();
    });

    it('defaults `shouldSearchValues()` to `false`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->shouldSearchValues())->toBeFalse();
    });

    it('can set `searchValues()`', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchValues();

        expect($checkboxList->shouldSearchValues())->toBeTrue();
    });

    it('returns `[label]` from `getSearchableOptionFields()` by default', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A']);

        expect($checkboxList->getSearchableOptionFields())->toBe(['label']);
    });

    it('returns `[value]` from `getSearchableOptionFields()` when only values searchable', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchLabels(false)
            ->searchValues();

        expect($checkboxList->getSearchableOptionFields())->toBe(['value']);
    });

    it('returns `[label, value]` from `getSearchableOptionFields()` when both searchable', function (): void {
        $checkboxList = CheckboxList::make('options')
            ->options(['a' => 'A'])
            ->searchValues();

        expect($checkboxList->getSearchableOptionFields())->toBe(['label', 'value']);
    });
});

describe('relationship name inference', function (): void {
    it('uses field name when `relationship()` name is `null`', function (): void {
        $checkboxList = CheckboxList::make('teams')
            ->relationship(titleAttribute: 'name');

        expect($checkboxList->getRelationshipName())->toBe('teams');
    });

    it('uses explicit name when provided to `relationship()`', function (): void {
        $checkboxList = CheckboxList::make('team_ids')
            ->relationship('teams', 'name');

        expect($checkboxList->getRelationshipName())->toBe('teams');
    });
});

describe('saving relationships', function (): void {
    it('can save selected options to a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(3);
    });

    it('can detach removed options from a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        // Deselect the third team
        livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => $teams->take(2)->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(2);
    });

    it('can save with `pivotData()` using `syncWithPivotValues()`', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();

        livewire(CheckboxListWithPivotData::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        // Query the pivot table directly since the relationship doesn't have withPivot('role')
        $pivotRows = DB::table('team_user')
            ->where('user_id', $user->id)
            ->get();

        expect($pivotRows)->toHaveCount(2);
        expect($pivotRows->first()->role)->toBe('member');
        expect($pivotRows->last()->role)->toBe('member');
    });

    it('can save empty state to detach all from a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => []])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(0);
    });

    it('applies `modifyQueryUsing` callback when saving a `BelongsToMany` relationship', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        $modifyCallCount = 0;
        CheckboxListWithBelongsToManyRelationshipAndModifyQueryThatCounts::$onModify = static function () use (&$modifyCallCount): void {
            $modifyCallCount++;
        };

        livewire(CheckboxListWithBelongsToManyRelationshipAndModifyQueryThatCounts::class, ['record' => $user])
            ->fillForm(['teams' => $teams->pluck('id')->map(fn ($id) => (string) $id)->all()])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(3);
        expect($modifyCallCount)->toBeGreaterThan(0);
    });

    it('invalidates the cached `BelongsToMany` relationship after save so a subsequent reload does not re-attach detached rows', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(2)->create();
        $user->teams()->attach($teams);

        $component = livewire(CheckboxListWithEagerLoadedBelongsToManyRelationship::class, ['record' => $user])
            ->fillForm(['teams' => []])
            ->call('save');

        expect($user->fresh()->teams)->toHaveCount(0)
            ->and($component->instance()->data['teams'])->toBe([]);

        $component->call('save');

        expect($user->fresh()->teams)->toHaveCount(0);
    });
});

describe('loading relationships', function (): void {
    it('loads attached `BelongsToMany` records when the relation is not eager-loaded', function (): void {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();
        $user->teams()->attach($teams);

        $freshUser = $user->fresh();
        expect($freshUser->relationLoaded('teams'))->toBeFalse();

        livewire(CheckboxListWithBelongsToManyRelationship::class, ['record' => $freshUser])
            ->assertSchemaStateSet(function (array $state) use ($teams): array {
                expect(collect($state['teams'])->sort()->values()->all())
                    ->toBe($teams->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all());

                return [];
            });
    });
});

describe('relationship options closure branches', function (): void {
    it('uses `getOptionDescriptionFromRecordUsing()` to set descriptions from relationship', function (): void {
        $user = User::factory()->create();
        Team::factory()->create(['name' => 'Alpha', 'description' => 'First team']);
        Team::factory()->create(['name' => 'Beta', 'description' => 'Second team']);

        livewire(CheckboxListWithRelationshipDescriptions::class, ['record' => $user])
            ->assertFormComponentExists('teams', function (CheckboxList $component): bool {
                $options = $component->getOptions();

                expect($options)->toHaveCount(2);

                // Descriptions should be set from the callback
                expect($component->hasDescription(array_key_first($options)))->toBeTrue();

                return true;
            });
    });
});

class CheckboxListWithPivotData extends Component implements HasActions, HasSchemas
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
                    ->pivotData(['role' => 'member']),
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

class CheckboxListWithRelationshipDescriptions extends Component implements HasActions, HasSchemas
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
                    ->getOptionDescriptionFromRecordUsing(fn (Team $record): string => $record->description ?? ''),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

describe('rendering', function (): void {
    it('can render with static `options()`', function (): void {
        livewire(RenderCheckboxListWithStaticOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Option A')
            ->assertSeeHtml('Option B');
    });

    it('can render with `options()` from `Closure`', function (): void {
        livewire(RenderCheckboxListWithClosureOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Dynamic A')
            ->assertSeeHtml('Dynamic B');
    });

    it('can render with `options()` from enum class string', function (): void {
        livewire(RenderCheckboxListWithEnumOptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('One')
            ->assertSeeHtml('Two')
            ->assertSeeHtml('Three');
    });

    it('can render with `bulkToggleable()`', function (): void {
        livewire(RenderCheckboxListWithBulkToggleable::class)
            ->assertSuccessful();
    });

    it('can render with `bulkToggleable()` set via `Closure`', function (): void {
        livewire(RenderCheckboxListWithBulkToggleableClosure::class)
            ->assertSuccessful();
    });

    it('can render with `searchable()`', function (): void {
        livewire(RenderCheckboxListWithSearchable::class)
            ->assertSuccessful();
    });

    it('can render with `searchable()` set via `Closure`', function (): void {
        livewire(RenderCheckboxListWithSearchableClosure::class)
            ->assertSuccessful();
    });

    it('can render with `allowHtml()`', function (): void {
        livewire(RenderCheckboxListWithAllowHtml::class)
            ->assertSuccessful();
    });

    it('can render with `allowHtml()` set via `Closure`', function (): void {
        livewire(RenderCheckboxListWithAllowHtmlClosure::class)
            ->assertSuccessful();
    });

    it('can render with `descriptions()`', function (): void {
        livewire(RenderCheckboxListWithDescriptions::class)
            ->assertSuccessful()
            ->assertSeeHtml('Description for A')
            ->assertSeeHtml('Description for B');
    });

    it('can render with `descriptions()` set via `Closure`', function (): void {
        livewire(RenderCheckboxListWithDescriptionsClosure::class)
            ->assertSuccessful()
            ->assertSeeHtml('Dynamic desc');
    });

    it('can render with `gridDirection()` enum', function (): void {
        livewire(RenderCheckboxListWithGridDirectionEnum::class)
            ->assertSuccessful();
    });

    it('can render with `gridDirection()` string', function (): void {
        livewire(RenderCheckboxListWithGridDirectionString::class)
            ->assertSuccessful();
    });

    it('can render with `gridDirection()` set via `Closure`', function (): void {
        livewire(RenderCheckboxListWithGridDirectionClosure::class)
            ->assertSuccessful();
    });

    it('can render with `searchPrompt()`', function (): void {
        livewire(RenderCheckboxListWithSearchPrompt::class)
            ->assertSuccessful()
            ->assertSeeHtml('Type to search...');
    });

    it('can render with `noSearchResultsMessage()`', function (): void {
        livewire(RenderCheckboxListWithNoSearchResultsMessage::class)
            ->assertSuccessful()
            ->assertSeeHtml('Nothing found');
    });

    it('can render with `searchDebounce()`', function (): void {
        livewire(RenderCheckboxListWithSearchDebounce::class)
            ->assertSuccessful();
    });

    it('can render with `disableOptionWhen()`', function (): void {
        livewire(RenderCheckboxListWithDisabledOptionWhen::class)
            ->assertSuccessful()
            ->assertSeeHtml('Active')
            ->assertSeeHtml('Archived');
    });
});

it('can render `CheckboxList` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/checkbox-list-test')
            ->assertSee('Test CheckboxList')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/checkbox-list-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderCheckboxListWithStaticOptions extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options([
                        'a' => 'Option A',
                        'b' => 'Option B',
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithClosureOptions extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(static fn (): array => [
                        'a' => 'Dynamic A',
                        'b' => 'Dynamic B',
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithEnumOptions extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(StringBackedEnum::class),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithBulkToggleable extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->bulkToggleable(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithBulkToggleableClosure extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->bulkToggleable(static fn (): bool => true),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithSearchable extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->searchable(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithSearchableClosure extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->searchable(static fn (): bool => true),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithAllowHtml extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => '<b>Bold</b>', 'b' => 'Plain'])
                    ->allowHtml(),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithAllowHtmlClosure extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => '<em>Italic</em>'])
                    ->allowHtml(static fn (): bool => true),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithDescriptions extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options([
                        'a' => 'Option A',
                        'b' => 'Option B',
                    ])
                    ->descriptions([
                        'a' => 'Description for A',
                        'b' => 'Description for B',
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithDescriptionsClosure extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A'])
                    ->descriptions(static fn (): array => ['a' => 'Dynamic desc']),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithGridDirectionEnum extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->gridDirection(GridDirection::Column),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithGridDirectionString extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->gridDirection('column'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithGridDirectionClosure extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->gridDirection(static fn (): string => 'column'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithSearchPrompt extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->searchable()
                    ->searchPrompt('Type to search...'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithNoSearchResultsMessage extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A'])
                    ->searchable()
                    ->noSearchResultsMessage('Nothing found'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithSearchDebounce extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options(['a' => 'A', 'b' => 'B'])
                    ->searchable()
                    ->searchDebounce(500),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class RenderCheckboxListWithDisabledOptionWhen extends Component implements HasActions, HasSchemas
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
                CheckboxList::make('items')
                    ->options([
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ])
                    ->disableOptionWhen(static fn (string $value): bool => $value === 'archived'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

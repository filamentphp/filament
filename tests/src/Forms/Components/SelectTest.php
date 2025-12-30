<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

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

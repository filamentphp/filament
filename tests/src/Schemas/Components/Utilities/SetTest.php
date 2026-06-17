<?php

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can set sibling state via `__invoke()`', function (): void {
    livewire(SetTestComponent::class)
        ->fillForm([
            'source' => '',
            'target' => '',
        ])
        ->fillForm([
            'source' => 'hello',
        ])
        ->assertFormSet([
            'target' => 'hello',
        ]);
});

it('can set state with a `Closure` value', function (): void {
    livewire(SetWithClosureComponent::class)
        ->fillForm([
            'source' => '',
            'target' => '',
        ])
        ->fillForm([
            'source' => 'world',
        ])
        ->assertFormSet([
            'target' => 'WORLD',
        ]);
});

it('returns the state value from `__invoke()`', function (): void {
    livewire(SetReturnsValueComponent::class)
        ->fillForm([
            'source' => '',
            'target' => '',
            'returned' => '',
        ])
        ->fillForm([
            'source' => 'test',
        ])
        ->assertFormSet([
            'target' => 'test',
            'returned' => 'test',
        ]);
});

it('clears cached default child schemas when setting a path without a component', function (): void {
    Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            $source = TextInput::make('source'),
            $repeater = Repeater::make('equipment_data.burners')
                ->schema([
                    TextInput::make('name'),
                ])
                ->default([
                    'old-burner' => ['name' => 'Old burner'],
                ]),
        ])
        ->fill();

    $repeater->getChildSchema('old-burner');

    $source->makeSetUtility()('equipment_data', [
        'burners' => [
            'new-burner' => ['name' => 'New burner'],
        ],
    ]);

    expect($repeater->getChildSchema('new-burner'))->not->toBeNull();
});

it('can set `skipComponentsChildContainersWhileSearching()`', function (): void {
    $component = (new Filament\Schemas\Components\Component)
        ->container(Schema::make(Livewire::make()));

    $set = new Set($component);

    $result = $set->skipComponentsChildContainersWhileSearching(false);

    expect($result)->toBe($set);
});

class SetTestComponent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('source')
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('target', $state);
                    }),
                TextInput::make('target'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SetWithClosureComponent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('source')
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('target', static fn (): string => strtoupper($state ?? ''));
                    }),
                TextInput::make('target'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

class SetReturnsValueComponent extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('source')
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                        $returned = $set('target', $state);
                        $set('returned', $returned);
                    }),
                TextInput::make('target'),
                TextInput::make('returned'),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('livewire.form');
    }
}

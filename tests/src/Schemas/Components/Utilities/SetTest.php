<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
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

it('does not recursively evaluate a named child schema `Closure` using `$set()`', function (): void {
    $footerEvaluationCount = 0;

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            $section = Section::make('Details')
                ->footer(function (Set $set) use (&$footerEvaluationCount): array {
                    $footerEvaluationCount++;

                    if ($footerEvaluationCount > 5) {
                        throw new RuntimeException('The footer schema was evaluated recursively.');
                    }

                    $set('unrepresentedValue', 'updated');

                    return [];
                }),
        ]);

    $schema->getComponents();
    $section->getChildSchemas(withHidden: true);

    expect($footerEvaluationCount)->toBe(1);
});

it('applies child state casts with `__invoke()`', function (): void {
    $livewire = Livewire::make();

    Schema::make($livewire)
        ->statePath('data')
        ->components([
            Tabs::make()
                ->tabs([
                    $parentTab = Tab::make('Parent')
                        ->schema([
                            Select::make('status')
                                ->options(StringBackedEnum::class),
                        ]),
                    $siblingTab = Tab::make('Sibling'),
                ]),
        ])
        ->fill(['status' => StringBackedEnum::One->value]);

    $parentTab->makeSetUtility()('status', StringBackedEnum::Two);

    expect($livewire->data['status'])
        ->toBe(StringBackedEnum::Two->value);

    $siblingTab->makeSetUtility()('status', StringBackedEnum::Three);

    expect($livewire->data['status'])
        ->toBe(StringBackedEnum::Three->value);
});

it('applies state casts from rebuilt child components with `__invoke()`', function (): void {
    $livewire = Livewire::make();

    Schema::make($livewire)
        ->statePath('data')
        ->components([
            $parentComponent = (new Filament\Schemas\Components\Component)
                ->schema([
                    Select::make('status')->options(StringBackedEnum::class),
                ]),
        ])
        ->fill(['status' => StringBackedEnum::One->value]);

    $set = $parentComponent->makeSetUtility();
    $set('status', StringBackedEnum::Two);

    expect($livewire->data['status'])
        ->toBe(StringBackedEnum::Two->value);

    $parentComponent->schema([
        TextInput::make('status'),
    ]);
    $parentComponent->getChildSchemas(withHidden: true)['default']->getComponents(withHidden: true);
    $set('status', StringBackedEnum::Three);

    expect($livewire->data['status'])
        ->toBe(StringBackedEnum::Three);
});

it('action-injected `Set` uses rebuilt nested child components', function (): void {
    $livewire = Livewire::make();

    Schema::make($livewire)
        ->statePath('data')
        ->components([
            (new Filament\Schemas\Components\Component)
                ->schema([
                    $parentComponent = (new Filament\Schemas\Components\Component)
                        ->schema([
                            Select::make('status')->options(StringBackedEnum::class),
                        ]),
                ]),
        ])
        ->fill(['status' => StringBackedEnum::One->value]);

    $state = StringBackedEnum::Two;
    $action = Action::make('setStatus')
        ->schemaComponent($parentComponent)
        ->action(function (Set $set) use (&$state): void {
            $set('status', $state);
        });

    $action->call();

    expect($livewire->data['status'])->toBe(StringBackedEnum::Two->value);

    $parentComponent->schema([
        TextInput::make('status'),
    ]);
    $state = StringBackedEnum::Three;
    $action->call();

    expect($livewire->data['status'])->toBe(StringBackedEnum::Three);
});

it('uses rebuilt builder item components for cached sibling `Set` lookups', function (): void {
    $livewire = Livewire::make();
    $builder = Builder::make('content')
        ->generateUuidUsing(false)
        ->blocks([
            Block::make('enum')->schema([
                Select::make('status')->options(StringBackedEnum::class),
            ]),
            Block::make('text')->schema([
                TextInput::make('status'),
            ]),
        ]);

    Schema::make($livewire)
        ->statePath('data')
        ->components([
            $source = TextInput::make('source'),
            $builder,
        ])
        ->fill([
            'content' => [[
                'type' => 'enum',
                'data' => ['status' => StringBackedEnum::One->value],
            ]],
        ]);

    $builder->getItems()[0]->getComponents();
    $set = $source->makeSetUtility();
    $set('/data.content.0.data.status', StringBackedEnum::Two);

    expect($livewire->data['content'][0]['data']['status'])->toBe(StringBackedEnum::Two->value);

    $livewire->data['content'][0]['type'] = 'text';
    $set('/data.content.0.data.status', StringBackedEnum::Three);

    expect($livewire->data['content'][0]['data']['status'])->toBe(StringBackedEnum::Three);
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

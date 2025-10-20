<?php

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

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

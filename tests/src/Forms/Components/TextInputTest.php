<?php

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can trim whitespace from TextInput', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTextInputTrim::class)
        ->fillForm(['name' => $input])
        ->call('save')
        ->assertSet('data.name', $expected);
})->with([
    ['  test value  ', 'test value'],
    ['test value', 'test value'],
    [null, null],
    ['', ''],
    [123, 123],
]);

it('can utilise multiple dehydrateStateUsing', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTextInputTrim::class)
        ->fillForm(['post_code' => $input])
        ->call('save')
        ->assertSet('data.post_code', $expected);
})->with([
    ['  po12 9AJ  ', 'PO12 9AJ'],
    ['po12 9AJ', 'PO12 9AJ'],
    [null, null],
    ['', ''],
    [123, 123],
]);

class TestComponentWithTextInputTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')->trim(),
                StackedDehydrateTestTextInput::make('post_code')->removeWhitespace()->capitalise(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

class StackedDehydrateTestTextInput extends TextInput
{
    public function removeWhitespace(): static
    {
        return $this->dehydrateStateUsing(fn ($state) => Str::squish($state));
    }

    public function capitalise(): static
    {
        return $this->dehydrateStateUsing(fn ($state) => Str::upper($state));
    }
}

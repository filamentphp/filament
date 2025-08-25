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

class TestComponentWithTextInputTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->trim(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

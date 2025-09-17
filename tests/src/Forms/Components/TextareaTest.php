<?php

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can trim whitespace from Textarea', function (mixed $input, mixed $expected): void {
    livewire(TestComponentWithTextareaTrim::class)
        ->fillForm(['description' => $input])
        ->call('save')
        ->assertSet('data.description', $expected);
})->with([
    ["  multiline\ntext content  ", "multiline\ntext content"],
    ['test content', 'test content'],
    [null, null],
    ['', ''],
    [123, 123],
]);

class TestComponentWithTextareaTrim extends Livewire
{
    public $data = [];

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->trim(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data = $this->form->getState();
    }
}

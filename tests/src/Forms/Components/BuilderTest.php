<?php

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('displays blocks in builder', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test',
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});

class TestComponentWithBuilder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }
}

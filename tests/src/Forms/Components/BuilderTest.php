<?php

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
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

it('can validate distinct fields in blocks in a builder with errors', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                ],
            ],
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data)
        ->call('save')
        ->assertHasFormErrors(['builder.0.data.foo' => ['The foo field has a duplicate value.'], 'builder.1.data.foo' => ['The foo field has a duplicate value.']]);
});

it('can validate distinct fields in blocks in a builder with no errors', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                ],
            ],
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 2',
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilder::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data)
        ->call('save')
        ->assertHasNoFormErrors();
});

it('can validate distinct fields in a repeater in a builder block with errors', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                    'repeater' => [
                        [
                            'bar' => 'test 1',
                        ],
                        [
                            'bar' => 'test 1',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                    'repeater' => [
                        [
                            'bar' => 'test 1',
                        ],
                        [
                            'bar' => 'test 1',
                        ],
                    ],
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilderAndRepeater::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data)
        ->call('save')
        ->assertHasFormErrors([
            'builder.0.data.foo' => ['The foo field has a duplicate value.'],
            'builder.0.data.repeater.0.bar' => ['The bar field has a duplicate value.'],
            'builder.0.data.repeater.1.bar' => ['The bar field has a duplicate value.'],
            'builder.1.data.foo' => ['The foo field has a duplicate value.'],
            'builder.1.data.repeater.0.bar' => ['The bar field has a duplicate value.'],
            'builder.1.data.repeater.1.bar' => ['The bar field has a duplicate value.'],
        ]);
});

it('can validate distinct fields in a repeater in a builder block with no errors', function (): void {
    $data = [
        'builder' => [
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 1',
                    'repeater' => [
                        [
                            'bar' => 'test 1',
                        ],
                        [
                            'bar' => 'test 2',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'one',
                'data' => [
                    'foo' => 'test 2',
                    'repeater' => [
                        [
                            'bar' => 'test 1',
                        ],
                        [
                            'bar' => 'test 2',
                        ],
                    ],
                ],
            ],
        ],
    ];

    livewire(TestComponentWithBuilderAndRepeater::class)
        ->assertSuccessful()
        ->fillForm($data)
        ->assertSchemaStateSet($data)
        ->call('save')
        ->assertHasNoFormErrors();
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
                                TextInput::make('foo')
                                    ->distinct(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithBuilderAndRepeater extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('builder')
                    ->blocks([
                        Builder\Block::make('one')
                            ->schema([
                                TextInput::make('foo')
                                    ->distinct(),
                                Repeater::make('repeater')
                                    ->schema([
                                        TextInput::make('bar')
                                            ->distinct(),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

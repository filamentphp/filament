<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

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

it('can access correct block schema state from action directly in builder schema', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithActionInBuilder::class)
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('blocks.0.data'),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Block 1 content',
        ])
        ->callAction(
            TestAction::make('captureSchemaState')
                ->schemaComponent('blocks.1.data'),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Block 2 content',
        ]);

    $undoBuilderFake();
});

it('can access correct block state from `extraItemActions()`', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithExtraItemActionInBuilder::class)
        ->callAction(
            TestAction::make('captureBlockState')
                ->schemaComponent('blocks')
                ->arguments(['item' => 0]),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'First Block',
        ])
        ->callAction(
            TestAction::make('captureBlockState')
                ->schemaComponent('blocks')
                ->arguments(['item' => 1]),
        )
        ->assertDispatched('state-captured', state: [
            'content' => 'Second Block',
        ]);

    $undoBuilderFake();
});

class TestComponentWithActionInBuilder extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->blocks([
                        Builder\Block::make('text')
                            ->schema([
                                TextInput::make('content'),
                                Action::make('captureSchemaState')
                                    ->action(function (array $schemaState): void {
                                        $this->dispatch('state-captured', state: $schemaState);
                                    }),
                            ]),
                    ])
                    ->default([
                        ['type' => 'text', 'data' => ['content' => 'Block 1 content']],
                        ['type' => 'text', 'data' => ['content' => 'Block 2 content']],
                    ]),
            ])
            ->statePath('data');
    }
}

class TestComponentWithExtraItemActionInBuilder extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Builder::make('blocks')
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->schema([
                                TextInput::make('content'),
                            ]),
                    ])
                    ->extraItemActions([
                        Action::make('captureBlockState')
                            ->action(function (array $schemaState): void {
                                $this->dispatch('state-captured', state: $schemaState);
                            }),
                    ])
                    ->default([
                        ['type' => 'paragraph', 'data' => ['content' => 'First Block']],
                        ['type' => 'paragraph', 'data' => ['content' => 'Second Block']],
                    ]),
            ])
            ->statePath('data');
    }
}

it('can add and delete blocks in the browser', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/builder-test')
        ->assertSee('Builder Test')
        ->assertSee('Content')
        ->assertNotPresent('[data-testid="builder"] .fi-fo-builder-item')
        ->click('text=Add to content')
        ->waitForText('Paragraph')
        ->click('text=Paragraph')
        ->wait(1)
        ->assertPresent('[data-testid="builder"] .fi-fo-builder-item')
        ->click('text=Add to content')
        ->waitForText('Heading')
        ->click('text=Heading')
        ->wait(1)
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 2)
        ->click('[data-testid="builder"] .fi-fo-builder-items > .fi-fo-builder-item:last-child .fi-fo-builder-item-header-end-actions button')
        ->wait(1)
        ->assertCount('[data-testid="builder"] .fi-fo-builder-item', 1)
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/builder-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

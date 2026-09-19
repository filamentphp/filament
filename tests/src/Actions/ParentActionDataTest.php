<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Actions\TestCase;
use Livewire\Attributes\Locked;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('reading the data of a parent action', function (): void {
    it('can use `getValidatedData()` to read the validated data of a parent action', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertDispatched('read-parent-data', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('throws a validation exception from `getValidatedData()` when the parent action is invalid', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => null,
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertHasErrors()
            ->assertNotDispatched('read-parent-data');
    });

    it('does not call `beforeStateDehydrated()` when `getValidatedData()` reads', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertDispatched('read-parent-data', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ])
            ->assertSet('parentDataDehydrationCount', 0);
    });

    it('calls `beforeStateDehydrated()` when the parent action is submitted', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callMountedAction()
            ->assertDispatched('parent-data-called')
            ->assertSet('parentDataDehydrationCount', 1);
    });
});

describe('filling the data of a parent action', function (): void {
    it('can use `fillParentActionData()` to write into a parent action', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->callAction(TestAction::make('fillParentData'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'generated',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('validates what was written with the rules of the parent action', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'valid',
            ])
            ->callAction(TestAction::make('fillParentDataWithInvalidValue'))
            ->callMountedAction()
            ->assertHasErrors(['mountedActions.0.data.reference' => 'max'])
            ->assertNotDispatched('parent-data-called');
    });

    it('can fill data exposed to a parent action by a schema component', function (): void {
        livewire(ParentActionData::class)
            ->mountAction(TestAction::make('submit')->schemaComponent('embeddedForm'))
            ->callAction(TestAction::make('fillParentFormData'))
            ->callMountedAction()
            ->assertSet('recordId', 1)
            ->assertDispatched('parent-form-called', data: [
                'foo' => 'filled',
                'emails' => ['alice@example.com'],
            ]);
    });
});

describe('reading a parent action from an action that is not registered on its modal', function (): void {
    it('can use `getValidatedParentActionData()` from an action registered on a schema component', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentDataFromComponent')->schemaComponent('reference'))
            ->assertDispatched('read-parent-data', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('throws from `getValidatedParentActionData()` when the action was not mounted from a parent action', function (): void {
        livewire(ParentActionData::class)
            ->callAction('readWithoutParent')
            ->assertNotDispatched('read-without-parent-called');
    })->throws(LogicException::class);

    it('reports invalid parent data before opening a nested action modal', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => null,
                'reference' => 'bar',
            ])
            ->mountAction('validateParentDataOnMount')
            ->assertActionMounted('parentData')
            ->assertCount('mountedActions', 1)
            ->assertHasErrors(['mountedActions.0.data.payload' => 'required']);
    });
});

describe('filling a parent action from an action that is not registered on its modal', function (): void {
    it('can use `fillParentActionData()` from an action registered on a schema component', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->callAction(TestAction::make('fillParentDataFromComponent')->schemaComponent('reference'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'from component',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('throws when the action was not mounted from a parent action', function (): void {
        livewire(ParentActionData::class)
            ->callAction('fillWithoutParent')
            ->assertNotDispatched('fill-without-parent-called');
    })->throws(LogicException::class);
});

describe('filling nested state of a parent action', function (): void {
    it('writes nested state and dot-notation keys where the parent action reads them', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('fillParentDataWithNesting'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => 'generated city'],
                'dotted' => ['postcode' => 'generated postcode'],
            ]);
    });
});

class ParentActionData extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $foo = 'original';

    /** @var array<int, array{email: string}> */
    public array $emails = [
        ['email' => 'old@example.com'],
        ['email' => 'stale@example.com'],
    ];

    #[Locked]
    public int $recordId = 1;

    public int $parentDataDehydrationCount = 0;

    public function fillWithoutParentAction(): Action
    {
        return Action::make('fillWithoutParent')
            ->action(function (Action $action): void {
                $action->fillParentActionData(['reference' => 'nowhere']);

                $this->dispatch('fill-without-parent-called');
            });
    }

    public function readWithoutParentAction(): Action
    {
        return Action::make('readWithoutParent')
            ->action(function (Action $action): void {
                $this->dispatch('read-without-parent-called', data: $action->getValidatedParentActionData());
            });
    }

    public function parentDataAction(): Action
    {
        return Action::make('parentData')
            ->schema([
                TextInput::make('payload')
                    ->required()
                    ->beforeStateDehydrated(function (): void {
                        $this->parentDataDehydrationCount++;
                    }),
                TextInput::make('reference')
                    ->required()
                    ->maxLength(15)
                    ->registerActions([
                        Action::make('fillParentDataFromComponent')
                            ->action(function (Action $action): void {
                                $action->fillParentActionData(['reference' => 'from component']);
                            }),
                        Action::make('readParentDataFromComponent')
                            ->action(function (Action $action): void {
                                $this->dispatch('read-parent-data', data: $action->getValidatedParentActionData());
                            }),
                    ]),
                Group::make([
                    TextInput::make('city'),
                ])->statePath('nested'),
                Group::make([
                    TextInput::make('postcode'),
                ])->statePath('dotted'),
            ])
            ->action(function (array $data): void {
                $this->dispatch('parent-data-called', data: $data);
            })
            ->extraModalFooterActions(fn (): array => [
                Action::make('fillParentDataWithNesting')
                    ->action(function (Action $action): void {
                        $action->fillParentActionData([
                            'nested' => ['city' => 'generated city'],
                            'dotted.postcode' => 'generated postcode',
                        ]);
                    }),
                Action::make('readParentData')
                    ->action(function (array $mountedActions): void {
                        $this->dispatch('read-parent-data', data: $mountedActions[0]->getValidatedData());
                    }),
                Action::make('validateParentDataOnMount')
                    ->schema([
                        TextInput::make('confirmation'),
                    ])
                    ->mountUsing(function (Action $action, Schema $schema): void {
                        $action->getValidatedParentActionData();

                        $schema->fill();
                    })
                    ->action(static fn (): null => null),
                Action::make('fillParentData')
                    ->action(function (Action $action): void {
                        $action->fillParentActionData(['reference' => 'generated']);
                    }),
                Action::make('fillParentDataWithInvalidValue')
                    ->action(function (Action $action): void {
                        $action->fillParentActionData(['reference' => 'value that is too long']);
                    }),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    TextInput::make('foo'),
                    Repeater::make('emails')
                        ->simple(TextInput::make('email')),
                ])
                    ->key('embeddedForm')
                    ->action(
                        Action::make('submit')
                            ->requiresConfirmation()
                            ->extraModalFooterActions([
                                Action::make('fillParentFormData')
                                    ->action(fn (Action $action) => $action->fillParentActionData([
                                        'foo' => 'filled',
                                        'emails' => ['alice@example.com'],
                                        'recordId' => 2,
                                    ])),
                            ])
                            ->action(function (array $data): void {
                                $this->dispatch('parent-form-called', data: $data);
                            }),
                    ),
            ]);
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}

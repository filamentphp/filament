<?php

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Livewire\Attributes\Locked;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('reading the data of a parent action', function (): void {
    it('injects `null` as `$parentAction` into a root action', function (): void {
        livewire(ParentActionData::class)
            ->callAction('readRootParent')
            ->assertDispatched('read-root-parent-action', parentAction: null);
    });

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

    it('keeps an invalid parent action mounted when the nested action uses `cancelParentActions()`', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => null,
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentDataAndCancel'))
            ->assertHasErrors()
            ->assertCount('mountedActions', 1)
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

describe('filling the data of a mounted action', function (): void {
    it('can use `fillData()` to write into a parent action', function (): void {
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

    it('can access different mounted ancestors', function (): void {
        livewire(ParentActionData::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->mountAction('intermediateData')
            ->assertSet('mountedActions.1.data.intermediate', 'parentData')
            ->setActionData([
                'intermediate' => null,
            ])
            ->callAction(TestAction::make('fillAncestorData'))
            ->assertSet('mountedActions.0.data.reference', 'top-generated')
            ->assertSet('mountedActions.1.data.intermediate', 'direct parent generated')
            ->assertDispatched(
                'read-ancestor-data',
                topMost: [
                    'payload' => 'foo',
                    'reference' => 'top-generated',
                    'nested' => ['city' => null],
                    'dotted' => ['postcode' => null],
                ],
                directParent: [
                    'intermediate' => 'direct parent generated',
                ],
            );
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

describe('accessing the data of an unmounted action', function (): void {
    it('does not use an action that was resolved without being mounted', function (): void {
        livewire(ParentActionData::class)
            ->call('fillUnmountedActionData')
            ->assertSet('mountedActions', [])
            ->assertDispatched('read-unmounted-action-data', data: []);
    });

    it('does not use an action after it is unmounted', function (): void {
        livewire(ParentActionData::class)
            ->call('fillUnmountedSchemaComponentActionData')
            ->assertSet('foo', 'original')
            ->assertSet('emails', [
                ['email' => 'old@example.com'],
                ['email' => 'stale@example.com'],
            ])
            ->assertSet('mountedActions', []);
    });

    it('does not use a stale action after another action replaces its nesting index', function (): void {
        livewire(ParentActionData::class)
            ->call('fillStaleActionDataAfterReplacement')
            ->assertSet('mountedActions.0.data.reference', 'replacement')
            ->assertDispatched('read-stale-action-data', rawData: [], validatedData: []);
    });

    it('does not use an unmounted clone of a mounted action', function (): void {
        livewire(ParentActionData::class)
            ->call('readClonedMountedActionData')
            ->assertSet('mountedActions.0.data.reference', 'mounted')
            ->assertDispatched('read-cloned-action-data', rawData: [], validatedData: []);
    });
});

describe('reading a parent action from an action that is not registered on its modal', function (): void {
    it('can use `getValidatedData()` from an action registered on a schema component', function (): void {
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

});

describe('validating parent data before mounting a nested action', function (): void {
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
    it('can use `fillData()` from an action registered on a schema component', function (): void {
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

describe('filling relationship state of a parent action', function (): void {
    it('replaces relationship selections and repeater items', function (): void {
        $user = User::factory()->create();
        $existingTeam = Team::factory()->create();
        $replacementTeam = Team::factory()->create();
        $user->teams()->attach($existingTeam);
        $existingPost = Post::factory()->create([
            'author_id' => $user->getKey(),
            'title' => 'Existing post',
        ]);

        livewire(ParentActionRelationshipData::class, ['record' => $user])
            ->mountAction('relationshipData')
            ->callAction(TestAction::make('replaceRelationshipData'))
            ->assertSet('mountedActions.0.data.teams', [(string) $replacementTeam->getKey()])
            ->assertSet('mountedActions.0.data.posts', function (array $posts): bool {
                $post = array_values($posts)[0] ?? null;

                return (count($posts) === 1) && ($post['title'] === 'Updated replacement post');
            })
            ->callMountedAction()
            ->assertHasNoErrors();

        expect($user->fresh()->teams->modelKeys())->toBe([$replacementTeam->getKey()])
            ->and($existingPost->fresh()->trashed())->toBeTrue()
            ->and($user->fresh()->posts()->pluck('title')->all())->toBe(['Updated replacement post']);
    });

    it('clears a relationship selection', function (): void {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team);

        livewire(ParentActionRelationshipData::class, ['record' => $user])
            ->mountAction('relationshipData')
            ->callAction(TestAction::make('clearRelationshipData'))
            ->assertSet('mountedActions.0.data.teams', [])
            ->callMountedAction()
            ->assertHasNoErrors();

        expect($user->fresh()->teams)->toBeEmpty();
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

    public function fillUnmountedActionData(): void
    {
        $action = $this->getAction('parentData');

        $action->fillData(['reference' => 'unexpected']);

        $this->dispatch('read-unmounted-action-data', data: $action->getValidatedData());
    }

    public function fillUnmountedSchemaComponentActionData(): void
    {
        $schema = $this->getSchema('form');
        $action = $schema?->getAction('submit', 'embeddedForm');

        if (! $action) {
            return;
        }

        $this->mountAction($action->getName(), context: $action->getContext());

        $action = $this->getMountedAction();

        if (! $action) {
            return;
        }

        $this->unmountAction();

        $action->fillData([
            'foo' => 'unexpected',
            'emails' => ['unexpected@example.com'],
        ]);
    }

    public function fillStaleActionDataAfterReplacement(): void
    {
        $this->mountAction('parentData');

        $staleAction = $this->getMountedAction();

        $this->unmountAction();
        $this->mountAction('replacementData');
        $this->mountedActions[0]['data']['reference'] = 'replacement';

        $staleAction->fillData(['reference' => 'unexpected']);

        $this->dispatch(
            'read-stale-action-data',
            rawData: $staleAction->getRawData(),
            validatedData: $staleAction->getValidatedData(),
        );
    }

    public function readClonedMountedActionData(): void
    {
        $this->mountAction('parentData');
        $this->mountedActions[0]['data']['reference'] = 'mounted';

        $clonedAction = clone $this->getMountedAction();

        $this->dispatch(
            'read-cloned-action-data',
            rawData: $clonedAction->getRawData(),
            validatedData: $clonedAction->getValidatedData(),
        );
    }

    public function replacementDataAction(): Action
    {
        return Action::make('replacementData')
            ->schema([
                TextInput::make('reference'),
            ])
            ->action(static fn () => null);
    }

    public function readRootParentAction(): Action
    {
        return Action::make('readRootParent')
            ->action(function (?Action $parentAction): void {
                $this->dispatch('read-root-parent-action', parentAction: $parentAction);
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
                            ->action(function (Action $parentAction): void {
                                $parentAction->fillData(['reference' => 'from component']);
                            }),
                        Action::make('readParentDataFromComponent')
                            ->action(function (Action $parentAction): void {
                                $this->dispatch('read-parent-data', data: $parentAction->getValidatedData());
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
                    ->action(function (Action $parentAction): void {
                        $parentAction->fillData([
                            'nested' => ['city' => 'generated city'],
                            'dotted.postcode' => 'generated postcode',
                        ]);
                    }),
                Action::make('intermediateData')
                    ->schema(fn (Action $parentAction): array => [
                        TextInput::make('intermediate')
                            ->default($parentAction->getName())
                            ->required(),
                    ])
                    ->extraModalFooterActions([
                        Action::make('fillAncestorData')
                            ->action(function (array $mountedActions, Action $parentAction): void {
                                $mountedActions[0]->fillData([
                                    'reference' => 'top-generated',
                                ]);
                                $parentAction->fillData([
                                    'intermediate' => 'direct parent generated',
                                ]);

                                $this->dispatch(
                                    'read-ancestor-data',
                                    topMost: $mountedActions[0]->getValidatedData(),
                                    directParent: $parentAction->getValidatedData(),
                                );
                            }),
                    ])
                    ->action(static fn (): null => null),
                Action::make('readParentData')
                    ->action(function (Action $parentAction): void {
                        $this->dispatch('read-parent-data', data: $parentAction->getValidatedData());
                    }),
                Action::make('readParentDataAndCancel')
                    ->cancelParentActions()
                    ->action(function (Action $parentAction): void {
                        $this->dispatch('read-parent-data', data: $parentAction->getValidatedData());
                    }),
                Action::make('validateParentDataOnMount')
                    ->schema([
                        TextInput::make('confirmation'),
                    ])
                    ->mountUsing(function (Action $parentAction, Schema $schema): void {
                        $parentAction->getValidatedData();

                        $schema->fill();
                    })
                    ->action(static fn (): null => null),
                Action::make('fillParentData')
                    ->action(function (Action $parentAction): void {
                        $parentAction->fillData(['reference' => 'generated']);
                    }),
                Action::make('fillParentDataWithInvalidValue')
                    ->action(function (Action $parentAction): void {
                        $parentAction->fillData(['reference' => 'value that is too long']);
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
                                    ->action(fn (Action $parentAction) => $parentAction->fillData([
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

class ParentActionRelationshipData extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public User $record;

    public function relationshipDataAction(): Action
    {
        return Action::make('relationshipData')
            ->record($this->record)
            ->schema([
                CheckboxList::make('teams')
                    ->relationship('teams', 'name'),
                Repeater::make('posts')
                    ->relationship('posts')
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                    ]),
            ])
            ->extraModalFooterActions([
                Action::make('replaceRelationshipData')
                    ->action(function (Action $parentAction): void {
                        $replacementTeam = Team::query()
                            ->whereKeyNot($this->record->teams()->first()->getKey())
                            ->firstOrFail();

                        $parentAction->fillData([
                            'teams' => [(string) $replacementTeam->getKey()],
                            'posts' => [
                                ['title' => 'Replacement post'],
                            ],
                        ]);

                        $postKey = array_key_first($parentAction->getRawData()['posts']);

                        $parentAction->fillData([
                            "posts.{$postKey}.title" => 'Updated replacement post',
                        ]);
                    }),
                Action::make('clearRelationshipData')
                    ->action(fn (Action $parentAction) => $parentAction->fillData(['teams' => []])),
            ])
            ->action(static fn () => null);
    }

    public function render(): string
    {
        return '<div></div>';
    }
}

<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\Actions;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can register actions', function (): void {
    $action = Action::make($actionName = Str::random());

    $component = TextInput::make('name')
        ->registerActions([$action]);

    expect($component->getAction($actionName))
        ->toBe($action);
});

it('can auto-register actions from affixes', function (): void {
    $component = TextInput::make('name')
        ->prefixAction(
            $prefixAction = Action::make($prefixActionName = Str::random()),
        )
        ->suffixAction(
            $suffixAction = Action::make($suffixActionName = Str::random()),
        );

    expect($component)
        ->getAction($prefixActionName)->toBe($prefixAction)
        ->getAction($suffixActionName)->toBe($suffixAction);
});

it('can call an action with data', function (): void {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('textInput'), data: [
            'value' => $value = Str::random(),
        ])
        ->assertSchemaStateSet(['textInput' => $value]);

    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertSchemaStateSet(['textInput' => $value]);
});

it('can validate an action\'s data', function (): void {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('textInput'), data: [
            'value' => null,
        ])
        ->assertHasFormErrors(['value' => ['required']])
        ->assertSchemaStateSet(['textInput' => null]);

    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasFormComponentActionErrors(['value' => ['required']])
        ->assertSchemaStateSet(['textInput' => null]);
});

it('can set default action data when mounted', function (): void {
    livewire(Actions::class)
        ->mountAction(TestAction::make('setValue')->schemaComponent('textInput'))
        ->assertSchemaStateSet([
            'value' => 'foo',
        ]);

    livewire(Actions::class)
        ->mountFormComponentAction('textInput', 'setValue')
        ->assertFormComponentActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function (): void {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValueFromArguments')->schemaComponent('textInput')->arguments([
            'value' => $value = Str::random(),
        ]))
        ->assertSchemaStateSet(['textInput' => $value]);

    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertSchemaStateSet(['textInput' => $value]);
});

it('can call an action and halt', function (): void {
    livewire(Actions::class)
        ->callAction(TestAction::make('halt')->schemaComponent('textInput'))
        ->assertActionHalted(TestAction::make('halt')->schemaComponent('textInput'));

    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'halt')
        ->assertFormComponentActionHalted('textInput', 'halt');
});

it('can hide an action', function (): void {
    livewire(Actions::class)
        ->assertActionHidden(TestAction::make('hidden')->schemaComponent('textInput'))
        ->assertActionVisible(TestAction::make('visible')->schemaComponent('textInput'))
        ->assertActionExists(TestAction::make('visible')->schemaComponent('textInput'), fn (Action $action): bool => $action->isVisible())
        ->assertActionExists(TestAction::make('hidden')->schemaComponent('textInput'), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('visible')->schemaComponent('textInput'), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('hidden')->schemaComponent('textInput'), fn (Action $action): bool => $action->isVisible());

    livewire(Actions::class)
        ->assertFormComponentActionVisible('textInput', 'visible')
        ->assertFormComponentActionHidden('textInput', 'hidden');
});

it('can disable an action', function (): void {
    livewire(Actions::class)
        ->assertActionDisabled(TestAction::make('disabled')->schemaComponent('textInput'))
        ->assertActionEnabled(TestAction::make('enabled')->schemaComponent('textInput'));

    livewire(Actions::class)
        ->assertFormComponentActionEnabled('textInput', 'enabled')
        ->assertFormComponentActionDisabled('textInput', 'disabled');
});

it('can have an icon', function (): void {
    livewire(Actions::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->schemaComponent('textInput'), Heroicon::PencilSquare)
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->schemaComponent('textInput'), Heroicon::Trash);

    livewire(Actions::class)
        ->assertFormComponentActionHasIcon('textInput', 'hasIcon', Heroicon::PencilSquare)
        ->assertFormComponentActionDoesNotHaveIcon('textInput', 'hasIcon', Heroicon::Trash);
});

it('can have a label', function (): void {
    livewire(Actions::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->schemaComponent('textInput'), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->schemaComponent('textInput'), 'My Other Action');

    livewire(Actions::class)
        ->assertFormComponentActionHasLabel('textInput', 'hasLabel', 'My Action')
        ->assertFormComponentActionDoesNotHaveLabel('textInput', 'hasLabel', 'My Other Action');
});

it('can have a color', function (): void {
    livewire(Actions::class)
        ->assertActionHasColor(TestAction::make('hasColor')->schemaComponent('textInput'), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->schemaComponent('textInput'), 'gray');

    livewire(Actions::class)
        ->assertFormComponentActionHasColor('textInput', 'hasColor', 'primary')
        ->assertFormComponentActionDoesNotHaveColor('textInput', 'hasColor', 'gray');
});

it('can have a URL', function (): void {
    livewire(Actions::class)
        ->assertActionHasUrl(TestAction::make('url')->schemaComponent('textInput'), 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl(TestAction::make('url')->schemaComponent('textInput'), 'https://google.com');

    livewire(Actions::class)
        ->assertFormComponentActionHasUrl('textInput', 'url', 'https://filamentphp.com')
        ->assertFormComponentActionDoesNotHaveUrl('textInput', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function (): void {
    livewire(Actions::class)
        ->assertActionShouldOpenUrlInNewTab(TestAction::make('urlInNewTab')->schemaComponent('textInput'))
        ->assertActionShouldNotOpenUrlInNewTab(TestAction::make('urlNotInNewTab')->schemaComponent('textInput'));

    livewire(Actions::class)
        ->assertFormComponentActionShouldOpenUrlInNewTab('textInput', 'urlInNewTab')
        ->assertFormComponentActionShouldNotOpenUrlInNewTab('textInput', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function (): void {
    livewire(Actions::class)
        ->assertActionExists(TestAction::make('exists')->schemaComponent('textInput'))
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->schemaComponent('textInput'));

    livewire(Actions::class)
        ->assertFormComponentActionExists('textInput', 'exists')
        ->assertFormComponentActionDoesNotExist('textInput', 'doesNotExist');
});

it('can get state from action registered on a component', function (): void {
    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureState')->schemaComponent('name'))
        ->assertDispatched('state-captured', state: 'John');
});

it('can get state from action in `belowContent` schema', function (): void {
    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureStateBelow')->schemaComponent('email'))
        ->assertDispatched('state-captured', state: 'john@example.com');
});

it('can get schema state from action directly in a schema with state path', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureSchemaState')->schemaComponent('items.0'))
        ->assertDispatched('state-captured', state: ['title' => 'Item 1'])
        ->callAction(TestAction::make('captureSchemaState')->schemaComponent('items.1'))
        ->assertDispatched('state-captured', state: ['title' => 'Item 2']);

    $undoRepeaterFake();
});

it('can get schema state from action in a Group without state path inside repeater', function (): void {
    $undoRepeaterFake = Repeater::fake();

    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureSchemaStateInGroup')->schemaComponent('items.0'))
        ->assertDispatched('state-captured', state: ['title' => 'Item 1']);

    $undoRepeaterFake();
});

it('can get schema state from action in builder block schema', function (): void {
    $undoBuilderFake = Builder::fake();

    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureSchemaState')->schemaComponent('blocks.0.data'))
        ->assertDispatched('state-captured', state: ['content' => 'Block 1'])
        ->callAction(TestAction::make('captureSchemaState')->schemaComponent('blocks.1.data'))
        ->assertDispatched('state-captured', state: ['content' => 'Block 2']);

    $undoBuilderFake();
});

it('can get state from action in `belowContent` schema inside a Group without state path', function (): void {
    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureStateBelowInGroup')->schemaComponent('phone'))
        ->assertDispatched('state-captured', state: '555-1234');
});

it('can get state from action directly in a Group with state path', function (): void {
    livewire(TestComponentWithActionState::class)
        ->callAction(TestAction::make('captureStateInGroupWithPath')->schemaComponent('address'))
        ->assertDispatched('state-captured', state: ['street' => '123 Main St', 'city' => 'Springfield']);
});

class TestComponentWithActionState extends Livewire
{
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')
                    ->default('John')
                    ->registerActions([
                        Action::make('captureState')
                            ->action(function (mixed $state): void {
                                $this->dispatch('state-captured', state: $state);
                            }),
                    ]),
                TextInput::make('email')
                    ->default('john@example.com')
                    ->belowContent([
                        Action::make('captureStateBelow')
                            ->action(function (mixed $state): void {
                                $this->dispatch('state-captured', state: $state);
                            }),
                    ]),
                Group::make([
                    TextInput::make('phone')
                        ->default('555-1234')
                        ->belowContent([
                            Action::make('captureStateBelowInGroup')
                                ->action(function (mixed $state): void {
                                    $this->dispatch('state-captured', state: $state);
                                }),
                        ]),
                ]),
                Group::make([
                    TextInput::make('street')
                        ->default('123 Main St'),
                    TextInput::make('city')
                        ->default('Springfield'),
                    Action::make('captureStateInGroupWithPath')
                        ->action(function (mixed $state): void {
                            $this->dispatch('state-captured', state: $state);
                        }),
                ])->statePath('address'),
                Repeater::make('items')
                    ->schema([
                        TextInput::make('title'),
                        Action::make('captureSchemaState')
                            ->action(function (mixed $schemaState): void {
                                $this->dispatch('state-captured', state: $schemaState);
                            }),
                        Group::make([
                            Action::make('captureSchemaStateInGroup')
                                ->action(function (mixed $schemaState): void {
                                    $this->dispatch('state-captured', state: $schemaState);
                                }),
                        ]),
                    ])
                    ->default([
                        ['title' => 'Item 1'],
                        ['title' => 'Item 2'],
                    ]),
                Builder::make('blocks')
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->schema([
                                TextInput::make('content'),
                                Action::make('captureSchemaState')
                                    ->action(function (mixed $schemaState): void {
                                        $this->dispatch('state-captured', state: $schemaState);
                                    }),
                            ]),
                    ])
                    ->default([
                        ['type' => 'paragraph', 'data' => ['content' => 'Block 1']],
                        ['type' => 'paragraph', 'data' => ['content' => 'Block 2']],
                    ]),
            ])
            ->statePath('data');
    }
}

<?php

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tests\Forms\Fixtures\Actions;
use Filament\Tests\Models\Post;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can register actions', function () {
    $action = Action::make($actionName = Str::random());

    $component = TextInput::make('name')
        ->registerActions([$action]);

    expect($component->getAction($actionName))
        ->toBe($action);
});

it('can auto-register actions from affixes', function () {
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

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertFormSet(['textInput' => $value]);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasFormComponentActionErrors(['value' => ['required']])
        ->assertFormSet(['textInput' => null]);
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountFormComponentAction('textInput', 'setValue')
        ->assertFormComponentActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertFormSet(['textInput' => $value]);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callFormComponentAction('textInput', 'halt')
        ->assertFormComponentActionHalted('textInput', 'halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertFormComponentActionVisible('textInput', 'visible')
        ->assertFormComponentActionHidden('textInput', 'hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertFormComponentActionEnabled('textInput', 'enabled')
        ->assertFormComponentActionDisabled('textInput', 'disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertFormComponentActionHasIcon('textInput', 'hasIcon', 'heroicon-m-pencil-square')
        ->assertFormComponentActionDoesNotHaveIcon('textInput', 'hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertFormComponentActionHasLabel('textInput', 'hasLabel', 'My Action')
        ->assertFormComponentActionDoesNotHaveLabel('textInput', 'hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertFormComponentActionHasColor('textInput', 'hasColor', 'primary')
        ->assertFormComponentActionDoesNotHaveColor('textInput', 'hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertFormComponentActionHasUrl('textInput', 'url', 'https://filamentphp.com')
        ->assertFormComponentActionDoesNotHaveUrl('textInput', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertFormComponentActionShouldOpenUrlInNewTab('textInput', 'urlInNewTab')
        ->assertFormComponentActionShouldNotOpenUrlInNewTab('textInput', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function () {
    livewire(Actions::class)
        ->assertFormComponentActionExists('textInput', 'exists')
        ->assertFormComponentActionDoesNotExist('textInput', 'doesNotExist');
});

it('only restores available form component action chains', function (string $restriction, bool $isNested): void {
    $component = livewire(RestoredFormActions::class, ['restriction' => $restriction])
        ->set([
            'mountedFormComponentActionsComponents' => $isNested ? ['textInput', 'nested-value'] : ['textInput'],
            'mountedFormComponentActions' => $isNested ? ['restricted', 'child'] : ['restricted'],
        ])
        ->call('$refresh');

    expect($component->instance()->getMountedFormComponentAction() !== null)->toBe($restriction === '');

    if (($restriction === '') && $isNested) {
        $component->call('callMountedFormComponentAction')->assertDispatched('restored-form-child-called')
            ->assertSet('mountedFormComponentActions', []);
    }

    if ($restriction !== '') {
        $component->call('callMountedFormComponentAction')->assertNotDispatched('restored-form-child-called')
            ->assertSet('mountedFormComponentActions', []);

        expect($component->instance()->getForm('mountedFormComponentActionForm0'))->toBeNull()
            ->and($component->instance()->getForm('mountedFormComponentActionForm1'))->toBeNull();
    }
})->with(['', 'hidden', 'invisible', 'disabled', 'unauthorized'])->with([false, true]);

it('preserves parent edits when mounting an unavailable form component child', function (string $actionName, bool $isNested): void {
    $component = livewire(RestoredFormActions::class)
        ->call('mountFormComponentAction', 'textInput', 'restricted')
        ->setFormComponentActionData(['value' => 'edited value']);

    if ($isNested) {
        $component->call('mountFormComponentAction', 'nested-value', 'child')
            ->setFormComponentActionData(['child_value' => 'edited child value']);
    }

    $component->call('mountFormComponentAction', $isNested ? 'child-value' : 'nested-value', $actionName)
        ->call('$refresh')
        ->assertSet('mountedFormComponentActions', $isNested ? ['restricted', 'child'] : ['restricted'])
        ->assertSet('mountedFormComponentActionsData.0.value', 'edited value');

    if ($isNested) {
        $component->assertFormComponentActionDataSet(['child_value' => 'edited child value']);
    }
})->with(['missing', 'unavailable'])->with([false, true]);

it('honors named parent cancellation when a fresh form component child is disabled', function (): void {
    livewire(RestoredFormActions::class)
        ->call('mountFormComponentAction', 'textInput', 'restricted')
        ->setFormComponentActionData(['value' => 'edited value'])
        ->call('mountFormComponentAction', 'nested-value', 'child')
        ->call('mountFormComponentAction', 'child-value', 'canceling-unavailable')
        ->assertSet('mountedFormComponentActions', ['restricted'])
        ->assertFormComponentActionDataSet(['value' => 'edited value']);
});

class RestoredFormActions extends Actions
{
    public string $restriction = '';

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            TextInput::make('textInput')->key('textInput')->registerActions([
                Action::make('restricted')
                    ->hidden(fn (): bool => $this->restriction === 'hidden')
                    ->visible(fn (): bool => $this->restriction !== 'invisible')
                    ->disabled(fn (): bool => $this->restriction === 'disabled')
                    ->authorize(fn (): bool => $this->restriction !== 'unauthorized')
                    ->form([
                        TextInput::make('value')->key('nested-value')->registerActions([
                            Action::make('unavailable')->disabled(),
                            Action::make('child')
                                ->form([
                                    TextInput::make('child_value')->key('child-value')->registerActions([
                                        Action::make('unavailable')->disabled(),
                                        Action::make('canceling-unavailable')->disabled()->cancelParentActions('child'),
                                    ]),
                                ])
                                ->disabled(fn (): bool => $this->restriction === 'completed')
                                ->cancelParentActions()
                                ->action(function (): void {
                                    $this->restriction = 'completed';
                                    $this->dispatch('restored-form-child-called');
                                }),
                        ]),
                    ]),
            ]),
        ]);
    }
}

it('refreshes a selected record before mounting its field action', function (bool $shouldRevokeSelection, bool $isNested): void {
    $author = User::factory()->create(['name' => 'Eligible author']);
    $livewire = livewire(SelectionRefreshFormActions::class)->instance();
    $livewire->form->fill(['author_id' => $author->getKey()]);
    $select = $livewire->form->getComponent('selected-author');

    expect($select->getSelectedRecord()->getKey())->toBe($author->getKey());

    if ($isNested) {
        $livewire->mountFormComponentAction('selected-author', 'inspect');
    }

    if ($shouldRevokeSelection) {
        $author->update(['name' => 'Excluded author']);
    }

    $livewire->mountFormComponentAction('selected-author', 'inspect', ['nested' => $isNested]);

    expect($livewire->selectedRecordKey)->toBe($shouldRevokeSelection ? null : $author->getKey())
        ->and($livewire->mountedFormComponentActions)->toHaveCount($isNested ? 2 : 1);
})->with([false, true])->with([false, true]);

class SelectionRefreshFormActions extends Actions
{
    public int | string | null $selectedRecordKey = null;

    public function form(Form $form): Form
    {
        return $form
            ->model(Post::class)
            ->statePath('data')
            ->schema([
                Select::make('author_id')
                    ->key('selected-author')
                    ->relationship('author', 'name', modifyQueryUsing: static fn (Builder $query): Builder => $query->where('name', 'Eligible author'))
                    ->suffixAction(
                        Action::make('inspect')
                            ->requiresConfirmation()
                            ->mountUsing(function (Select $component): void {
                                $this->selectedRecordKey = $component->getSelectedRecord()?->getKey();
                            }),
                    ),
            ]);
    }
}

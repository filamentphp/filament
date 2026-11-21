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

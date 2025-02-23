<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\InfolistActions;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can call an action with data', function (): void {
    livewire(InfolistActions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'), data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);

    livewire(InfolistActions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can validate an action\'s data', function (): void {
    livewire(InfolistActions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'), data: [
            'value' => null,
        ])
        ->assertHasActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');

    livewire(InfolistActions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasInfolistActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');
});

it('can set default action data when mounted', function (): void {
    livewire(InfolistActions::class)
        ->mountAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'))
        ->assertActionDataSet([
            'value' => 'foo',
        ]);

    livewire(InfolistActions::class)
        ->mountInfolistAction('textEntry', 'setValue')
        ->assertInfolistActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function (): void {
    livewire(InfolistActions::class)
        ->callAction(TestAction::make('setValueFromArguments')->schemaComponent('infolist.textEntry')->arguments([
            'value' => $value = Str::random(),
        ]))
        ->assertDispatched('foo', $value);

    livewire(InfolistActions::class)
        ->callInfolistAction('textEntry', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can call an action and halt', function (): void {
    livewire(InfolistActions::class)
        ->callAction(TestAction::make('halt')->schemaComponent('infolist.textEntry'))
        ->assertActionHalted(TestAction::make('halt')->schemaComponent('infolist.textEntry'));

    livewire(InfolistActions::class)
        ->callInfolistAction('textEntry', 'halt')
        ->assertInfolistActionHalted('textEntry', 'halt');
});

it('can hide an action', function (): void {
    livewire(InfolistActions::class)
        ->assertActionVisible(TestAction::make('visible')->schemaComponent('infolist.textEntry'))
        ->assertActionHidden(TestAction::make('hidden')->schemaComponent('infolist.textEntry'))
        ->assertActionExists(TestAction::make('visible')->schemaComponent('infolist.textEntry'), fn (Action $action): bool => $action->isVisible())
        ->assertActionExists(TestAction::make('hidden')->schemaComponent('infolist.textEntry'), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('visible')->schemaComponent('infolist.textEntry'), fn (Action $action): bool => $action->isHidden())
        ->assertActionDoesNotExist(TestAction::make('hidden')->schemaComponent('infolist.textEntry'), fn (Action $action): bool => $action->isVisible());

    livewire(InfolistActions::class)
        ->assertInfolistActionVisible('textEntry', 'visible')
        ->assertInfolistActionHidden('textEntry', 'hidden');
});

it('can disable an action', function (): void {
    livewire(InfolistActions::class)
        ->assertActionEnabled(TestAction::make('enabled')->schemaComponent('infolist.textEntry'))
        ->assertActionDisabled(TestAction::make('disabled')->schemaComponent('infolist.textEntry'));

    livewire(InfolistActions::class)
        ->assertInfolistActionEnabled('textEntry', 'enabled')
        ->assertInfolistActionDisabled('textEntry', 'disabled');
});

it('can have an icon', function (): void {
    livewire(InfolistActions::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->schemaComponent('infolist.textEntry'), Heroicon::PencilSquare)
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->schemaComponent('infolist.textEntry'), Heroicon::Trash);

    livewire(InfolistActions::class)
        ->assertInfolistActionHasIcon('textEntry', 'hasIcon', Heroicon::PencilSquare)
        ->assertInfolistActionDoesNotHaveIcon('textEntry', 'hasIcon', Heroicon::Trash);
});

it('can have a label', function (): void {
    livewire(InfolistActions::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->schemaComponent('infolist.textEntry'), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->schemaComponent('infolist.textEntry'), 'My Other Action');

    livewire(InfolistActions::class)
        ->assertInfolistActionHasLabel('textEntry', 'hasLabel', 'My Action')
        ->assertInfolistActionDoesNotHaveLabel('textEntry', 'hasLabel', 'My Other Action');
});

it('can have a color', function (): void {
    livewire(InfolistActions::class)
        ->assertActionHasColor(TestAction::make('hasColor')->schemaComponent('infolist.textEntry'), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->schemaComponent('infolist.textEntry'), 'gray');

    livewire(InfolistActions::class)
        ->assertInfolistActionHasColor('textEntry', 'hasColor', 'primary')
        ->assertInfolistActionDoesNotHaveColor('textEntry', 'hasColor', 'gray');
});

it('can have a URL', function (): void {
    livewire(InfolistActions::class)
        ->assertActionHasUrl(TestAction::make('url')->schemaComponent('infolist.textEntry'), 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl(TestAction::make('url')->schemaComponent('infolist.textEntry'), 'https://google.com');

    livewire(InfolistActions::class)
        ->assertInfolistActionHasUrl('textEntry', 'url', 'https://filamentphp.com')
        ->assertInfolistActionDoesNotHaveUrl('textEntry', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function (): void {
    livewire(InfolistActions::class)
        ->assertActionShouldOpenUrlInNewTab(TestAction::make('urlInNewTab')->schemaComponent('infolist.textEntry'))
        ->assertActionShouldNotOpenUrlInNewTab(TestAction::make('urlNotInNewTab')->schemaComponent('infolist.textEntry'));

    livewire(InfolistActions::class)
        ->assertInfolistActionShouldOpenUrlInNewTab('textEntry', 'urlInNewTab')
        ->assertInfolistActionShouldNotOpenUrlInNewTab('textEntry', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function (): void {
    livewire(InfolistActions::class)
        ->assertActionExists(TestAction::make('exists')->schemaComponent('infolist.textEntry'))
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->schemaComponent('infolist.textEntry'));

    livewire(InfolistActions::class)
        ->assertInfolistActionExists('textEntry', 'exists')
        ->assertInfolistActionDoesNotExist('textEntry', 'doesNotExist');
});

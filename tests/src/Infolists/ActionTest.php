<?php

use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Tests\Infolists\Fixtures\Actions;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'), data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);

    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'), data: [
            'value' => null,
        ])
        ->assertHasActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');

    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasInfolistActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountAction(TestAction::make('setValue')->schemaComponent('infolist.textEntry'))
        ->assertActionDataSet([
            'value' => 'foo',
        ]);

    livewire(Actions::class)
        ->mountInfolistAction('textEntry', 'setValue')
        ->assertInfolistActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callAction(TestAction::make('setValueFromArguments')->schemaComponent('infolist.textEntry')->arguments([
            'value' => $value = Str::random(),
        ]))
        ->assertDispatched('foo', $value);

    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callAction(TestAction::make('halt')->schemaComponent('infolist.textEntry'))
        ->assertActionHalted(TestAction::make('halt')->schemaComponent('infolist.textEntry'));

    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'halt')
        ->assertInfolistActionHalted('textEntry', 'halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertActionVisible(TestAction::make('visible')->schemaComponent('infolist.textEntry'))
        ->assertActionHidden(TestAction::make('hidden')->schemaComponent('infolist.textEntry'));

    livewire(Actions::class)
        ->assertInfolistActionVisible('textEntry', 'visible')
        ->assertInfolistActionHidden('textEntry', 'hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertActionEnabled(TestAction::make('enabled')->schemaComponent('infolist.textEntry'))
        ->assertActionDisabled(TestAction::make('disabled')->schemaComponent('infolist.textEntry'));

    livewire(Actions::class)
        ->assertInfolistActionEnabled('textEntry', 'enabled')
        ->assertInfolistActionDisabled('textEntry', 'disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertActionHasIcon(TestAction::make('hasIcon')->schemaComponent('infolist.textEntry'), 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon(TestAction::make('hasIcon')->schemaComponent('infolist.textEntry'), 'heroicon-m-trash');

    livewire(Actions::class)
        ->assertInfolistActionHasIcon('textEntry', 'hasIcon', 'heroicon-m-pencil-square')
        ->assertInfolistActionDoesNotHaveIcon('textEntry', 'hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertActionHasLabel(TestAction::make('hasLabel')->schemaComponent('infolist.textEntry'), 'My Action')
        ->assertActionDoesNotHaveLabel(TestAction::make('hasLabel')->schemaComponent('infolist.textEntry'), 'My Other Action');

    livewire(Actions::class)
        ->assertInfolistActionHasLabel('textEntry', 'hasLabel', 'My Action')
        ->assertInfolistActionDoesNotHaveLabel('textEntry', 'hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertActionHasColor(TestAction::make('hasColor')->schemaComponent('infolist.textEntry'), 'primary')
        ->assertActionDoesNotHaveColor(TestAction::make('hasColor')->schemaComponent('infolist.textEntry'), 'gray');

    livewire(Actions::class)
        ->assertInfolistActionHasColor('textEntry', 'hasColor', 'primary')
        ->assertInfolistActionDoesNotHaveColor('textEntry', 'hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertActionHasUrl(TestAction::make('url')->schemaComponent('infolist.textEntry'), 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl(TestAction::make('url')->schemaComponent('infolist.textEntry'), 'https://google.com');

    livewire(Actions::class)
        ->assertInfolistActionHasUrl('textEntry', 'url', 'https://filamentphp.com')
        ->assertInfolistActionDoesNotHaveUrl('textEntry', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertActionShouldOpenUrlInNewTab(TestAction::make('urlInNewTab')->schemaComponent('infolist.textEntry'))
        ->assertActionShouldNotOpenUrlInNewTab(TestAction::make('urlNotInNewTab')->schemaComponent('infolist.textEntry'));

    livewire(Actions::class)
        ->assertInfolistActionShouldOpenUrlInNewTab('textEntry', 'urlInNewTab')
        ->assertInfolistActionShouldNotOpenUrlInNewTab('textEntry', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function () {
    livewire(Actions::class)
        ->assertActionExists(TestAction::make('exists')->schemaComponent('infolist.textEntry'))
        ->assertActionDoesNotExist(TestAction::make('doesNotExist')->schemaComponent('infolist.textEntry'));

    livewire(Actions::class)
        ->assertInfolistActionExists('textEntry', 'exists')
        ->assertInfolistActionDoesNotExist('textEntry', 'doesNotExist');
});

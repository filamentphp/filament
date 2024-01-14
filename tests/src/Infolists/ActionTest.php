<?php

use Filament\Tests\Infolists\Fixtures\Actions;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValue', data: [
            'value' => null,
        ])
        ->assertHasInfolistActionErrors(['value' => ['required']])
        ->assertNotDispatched('foo');
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountInfolistAction('textEntry', 'setValue')
        ->assertInfolistActionDataSet([
            'value' => 'foo',
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'setValueFromArguments', arguments: [
            'value' => $value = Str::random(),
        ])
        ->assertDispatched('foo', $value);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callInfolistAction('textEntry', 'halt')
        ->assertInfolistActionHalted('textEntry', 'halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertInfolistActionVisible('textEntry', 'visible')
        ->assertInfolistActionHidden('textEntry', 'hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertInfolistActionEnabled('textEntry', 'enabled')
        ->assertInfolistActionDisabled('textEntry', 'disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasIcon('textEntry', 'hasIcon', 'heroicon-m-pencil-square')
        ->assertInfolistActionDoesNotHaveIcon('textEntry', 'hasIcon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasLabel('textEntry', 'hasLabel', 'My Action')
        ->assertInfolistActionDoesNotHaveLabel('textEntry', 'hasLabel', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasColor('textEntry', 'hasColor', 'primary')
        ->assertInfolistActionDoesNotHaveColor('textEntry', 'hasColor', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertInfolistActionHasUrl('textEntry', 'url', 'https://filamentphp.com')
        ->assertInfolistActionDoesNotHaveUrl('textEntry', 'url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertInfolistActionShouldOpenUrlInNewTab('textEntry', 'urlInNewTab')
        ->assertInfolistActionShouldNotOpenUrlInNewTab('textEntry', 'urlNotInNewTab');
});

it('can state whether a form component action exists', function () {
    livewire(Actions::class)
        ->assertInfolistActionExists('textEntry', 'exists')
        ->assertInfolistActionDoesNotExist('textEntry', 'doesNotExist');
});

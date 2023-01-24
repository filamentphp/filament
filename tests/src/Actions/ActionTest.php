<?php

use Filament\Tests\Actions\Fixtures\Pages\Actions;
use Filament\Tests\Actions\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(Actions::class)
        ->callAction('simple')
        ->assertEmitted('simple-called');
});

it('can call an action with data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(Actions::class)
        ->callAction('data', data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
});

it('can set default action data when mounted', function () {
    livewire(Actions::class)
        ->mountAction('data')
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can mount an action with arguments', function () {
    livewire(Actions::class)
        ->mountAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->callMountedAction()
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action with arguments', function () {
    livewire(Actions::class)
        ->callAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(Actions::class)
        ->callAction('halt')
        ->assertEmitted('halt-called')
        ->assertActionHalted('halt');
});

it('can hide an action', function () {
    livewire(Actions::class)
        ->assertActionVisible('visible')
        ->assertActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(Actions::class)
        ->assertActionEnabled('enabled')
        ->assertActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(Actions::class)
        ->assertActionHasIcon('has-icon', 'heroicon-m-pencil-square')
        ->assertActionDoesNotHaveIcon('has-icon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(Actions::class)
        ->assertActionHasLabel('has-label', 'My Action')
        ->assertActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(Actions::class)
        ->assertActionHasColor('has-color', 'primary')
        ->assertActionDoesNotHaveColor('has-color', 'gray');
});

it('can have a URL', function () {
    livewire(Actions::class)
        ->assertActionHasUrl('url', 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(Actions::class)
        ->assertActionShouldOpenUrlInNewTab('url_in_new_tab')
        ->assertActionShouldNotOpenUrlInNewTab('url_not_in_new_tab');
});

it('can state whether a page action exists', function () {
    livewire(Actions::class)
        ->assertActionExists('exists')
        ->assertActionDoesNotExist('does_not_exist');
});

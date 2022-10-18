<?php

use Filament\Tests\App\Fixtures\Pages\PageActions;
use Filament\Tests\App\Pages\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(PageActions::class)
        ->callAction('simple')
        ->assertEmitted('simple-called');
});

it('can call an action with data', function () {
    livewire(PageActions::class)
        ->callAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PageActions::class)
        ->callAction('data', data: [
            'payload' => null,
        ])
        ->assertHasActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PageActions::class)
        ->mountAction('data')
        ->assertActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call an action with arguments', function () {
    livewire(PageActions::class)
        ->callAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and halt', function () {
    livewire(PageActions::class)
        ->callAction('halt')
        ->assertEmitted('halt-called')
        ->assertActionHalted('halt');
});

it('can hide an action', function () {
    livewire(PageActions::class)
        ->assertActionVisible('visible')
        ->assertActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(PageActions::class)
        ->assertActionEnabled('enabled')
        ->assertActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PageActions::class)
        ->assertActionHasIcon('has-icon', 'heroicon-m-pencil')
        ->assertActionDoesNotHaveIcon('has-icon', 'heroicon-m-trash');
});

it('can have a label', function () {
    livewire(PageActions::class)
        ->assertActionHasLabel('has-label', 'My Action')
        ->assertActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(PageActions::class)
        ->assertActionHasColor('has-color', 'primary')
        ->assertActionDoesNotHaveColor('has-color', 'secondary');
});

it('can have a URL', function () {
    livewire(PageActions::class)
        ->assertActionHasUrl('url', 'https://filamentphp.com')
        ->assertActionDoesNotHaveUrl('url', 'https://google.com');
});

it('can open a URL in a new tab', function () {
    livewire(PageActions::class)
        ->assertActionShouldOpenUrlInNewTab('url_in_new_tab')
        ->assertActionShouldNotOpenUrlInNewTab('url_not_in_new_tab');
});

it('can state whether a page action exists', function () {
    livewire(PageActions::class)
        ->assertActionExists('exists')
        ->assertActionDoesNotExist('does_not_exist');
});

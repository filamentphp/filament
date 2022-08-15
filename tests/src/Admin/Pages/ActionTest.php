<?php

use Filament\Tests\Admin\Fixtures\Pages\PageActions;
use Filament\Tests\Admin\Pages\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can call an action', function () {
    livewire(PageActions::class)
        ->callPageAction('simple')
        ->assertEmitted('simple-called');
});

it('can call an action with data', function () {
    livewire(PageActions::class)
        ->callPageAction('data', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoPageActionErrors()
        ->assertEmitted('data-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PageActions::class)
        ->callPageAction('data', data: [
            'payload' => null,
        ])
        ->assertHasPageActionErrors(['payload' => ['required']])
        ->assertNotEmitted('data-called');
});

it('can set default action data when mounted', function () {
    livewire(PageActions::class)
        ->mountPageAction('data')
        ->assertPageActionDataSet([
            'foo' => 'bar',
        ]);
});

it('can call an action with arguments', function () {
    livewire(PageActions::class)
        ->callPageAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call an action and hold', function () {
    livewire(PageActions::class)
        ->callPageAction('hold')
        ->assertEmitted('hold-called')
        ->assertPageActionHeld('hold');
});

it('can hide an action', function () {
    livewire(PageActions::class)
        ->assertPageActionVisible('visible')
        ->assertPageActionHidden('hidden');
});

it('can disable an action', function () {
    livewire(PageActions::class)
        ->assertPageActionEnabled('enabled')
        ->assertPageActionDisabled('disabled');
});

it('can have an icon', function () {
    livewire(PageActions::class)
        ->assertPageActionHasIcon('has-icon', 'heroicon-s-pencil')
        ->assertPageActionDoesNotHaveIcon('has-icon', 'heroicon-o-trash');
});

it('can have a label', function () {
    livewire(PageActions::class)
        ->assertPageActionHasLabel('has-label', 'My Action')
        ->assertPageActionDoesNotHaveLabel('has-label', 'My Other Action');
});

it('can have a color', function () {
    livewire(PageActions::class)
        ->assertPageActionHasColor('has-color', 'primary')
        ->assertPageActionDoesNotHaveColor('has-color', 'secondary');
});

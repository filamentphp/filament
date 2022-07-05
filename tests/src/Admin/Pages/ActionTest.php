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
        ->callPageAction('form', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoPageActionErrors()
        ->assertEmitted('form-called', [
            'payload' => $payload,
        ]);
});

it('can validate an action\'s data', function () {
    livewire(PageActions::class)
        ->callPageAction('form', data: [
            'payload' => null,
        ])
        ->assertHasPageActionErrors(['payload' => ['required']])
        ->assertNotEmitted('form-called');
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

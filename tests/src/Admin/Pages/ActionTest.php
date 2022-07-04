<?php

use Filament\Tests\Admin\Fixtures\Pages\PageActions;
use Filament\Tests\Admin\Pages\TestCase;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(TestCase::class);

it('can call a page action', function () {
    Livewire::test(PageActions::class)
        ->callPageAction('simple')
        ->assertEmitted('simple-called');
});

it('can call a page action with data', function () {
    Livewire::test(PageActions::class)
        ->callPageAction('form', data: [
            'payload' => $payload = Str::random(),
        ])
        ->assertHasNoPageActionErrors()
        ->assertEmitted('form-called', [
            'payload' => $payload,
        ]);
});

it('can validate a page action form data', function () {
    Livewire::test(PageActions::class)
        ->callPageAction('form', data: [
            'payload' => null,
        ])
        ->assertHasPageActionErrors(['payload' => 'required']);
});

it('can call a page action with arguments', function () {
    Livewire::test(PageActions::class)
        ->callPageAction('arguments', arguments: [
            'payload' => $payload = Str::random(),
        ])
        ->assertEmitted('arguments-called', [
            'payload' => $payload,
        ]);
});

it('can call a page action and hold', function () {
    Livewire::test(PageActions::class)
        ->callPageAction('hold')
        ->assertEmitted('hold-called')
        ->assertPageActionHeld('hold');
});

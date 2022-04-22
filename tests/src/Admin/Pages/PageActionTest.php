<?php

use Filament\Tests\Admin\Fixtures\Pages\Actions;
use Filament\Tests\Admin\Pages\TestCase;
use Livewire\Livewire;

uses(TestCase::class);

it('can call a page action', function () {
    Livewire::test(Actions::class)
        ->callPageAction('foo')
        ->assertSuccessful()
        ->assertDispatchedBrowserEvent('foo-called');
});

it('can call a page action with data', function () {
    Livewire::test(Actions::class)
        ->callPageAction('foo-form', [
            'name' => 'Ryan',
        ])
        ->assertHasNoErrors()
        ->assertDispatchedBrowserEvent('foo-form-called', [
            'name' => 'Ryan',
        ]);
});

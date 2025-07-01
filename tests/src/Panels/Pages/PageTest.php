<?php

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tests\Panels\Fixtures\Pages\HookPage;
use Filament\Tests\Panels\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Validation\ValidationException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Settings::getUrl())
        ->assertSuccessful();
});

it('can generate a slug based on the page name', function () {
    expect(Settings::getSlug())
        ->toBe('settings');
});

it('can report validation errors', function () {
    Page::$reportValidationErrorUsing = function (ValidationException $exception) {
        Notification::make()
            ->danger()
            ->title($exception->getMessage())
            ->send();
    };

    $component = livewire(Settings::class);

    $component
        ->call('save')
        ->assertHasErrors(['name' => ['required']])
        ->assertNotified();
});

it('can call macro hooks', function () {

    $internalVar = null;

    // apply the macro to the HookPage class here
    // i.e. pretend we don't control the page definition
    HookPage::macro('afterSave', function () use (&$internalVar) {
        $internalVar = 'macro called';
    });

    livewire(HookPage::class)
        ->fill([
            'name' => 'Macro User',
        ])
        ->call('save');

    expect($internalVar)->toBe('macro called');
});

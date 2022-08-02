<?php

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Pages\TestCase;
use Illuminate\Validation\ValidationException;
use Livewire\LivewireManager;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Settings::getUrl())->assertSuccessful();
});

it('can generate a slug based on the page name', function () {
    expect(Settings::getSlug())
        ->toBe('settings');
});

it('can report validation errors', function () {
    Page::$reportValidationErrorUsing = function (ValidationException $exception) {
        Filament::notify('danger', $exception->getMessage());
    };

    $component = livewire(Settings::class);

    LivewireManager::$isLivewireRequestTestingOverride = true;

    $component
        ->call('save')
        ->assertHasErrors(['name' => ['required']])
        ->assertNotified();
});

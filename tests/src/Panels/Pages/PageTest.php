<?php

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tests\Panels\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\HtmlString;
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

it('strips html tags from brand name in title tag', function () {
    $htmlBrandName = '<span>Filament <i>HTML Title</i></span>';

    Filament::getCurrentPanel()->brandName(new HtmlString($htmlBrandName));

    $this->get(Settings::getUrl())
        ->assertSee('Filament HTML Title')
        ->assertDontSee($htmlBrandName);
});

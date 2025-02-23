<?php

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Validation\ValidationException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(Settings::getUrl())
        ->assertSuccessful();
});

it('can generate a slug based on the page name', function (): void {
    expect(Settings::getSlug())
        ->toBe('settings');
});

it('can report validation errors', function (): void {
    Page::$reportValidationErrorUsing = function (ValidationException $exception): void {
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

<?php

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tests\Fixtures\Pages\AuthorizableSettings;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Validation\ValidationException;
use Livewire\Exceptions\MethodNotFoundException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $this->get(Settings::getUrl())
        ->assertSuccessful();
});

it('allows exposed page listeners but blocks frontend calls to internal methods', function (): void {
    $component = livewire(Settings::class)
        ->dispatch('refresh-page')
        ->assertSuccessful();

    expect($component->instance()->getTitle())->toBe('Settings');

    expect(fn () => $component->call('getTitle'))
        ->toThrow(MethodNotFoundException::class);
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

it('re-authorizes a custom page on Livewire updates after the initial mount', function (): void {
    AuthorizableSettings::$canAccess = true;

    $component = livewire(AuthorizableSettings::class);

    AuthorizableSettings::$canAccess = false;

    $component
        ->set('name', 'foo')
        ->assertStatus(403);

    AuthorizableSettings::$canAccess = true;
});

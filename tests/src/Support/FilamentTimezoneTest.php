<?php

use Filament\Support\Facades\FilamentTimezone;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be set to a specific timezone', function (): void {
    FilamentTimezone::set('America/Vancouver');

    // Simulate what happens when the facade resolves a fresh instance
    // (e.g., across Livewire requests or when facade cache is cleared)
    FilamentTimezone::clearResolvedInstance(
        \Filament\Support\TimezoneManager::class,
    );

    expect(FilamentTimezone::get())->toBe('America/Vancouver');
});

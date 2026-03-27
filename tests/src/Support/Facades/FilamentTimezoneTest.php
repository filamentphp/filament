<?php

use Filament\Support\Facades\FilamentTimezone;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be set to a specific timezone', function (): void {
    FilamentTimezone::set('America/Vancouver');

    expect(FilamentTimezone::get())->toBe('America/Vancouver');
});

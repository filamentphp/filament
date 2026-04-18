<?php

use Filament\FilamentManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can retrieve `FilamentManager` from container', function (): void {
    $this->assertInstanceOf(
        FilamentManager::class,
        filament(),
    );
});

it('returns the same `FilamentManager` instance on repeated calls', function (): void {
    expect(filament())->toBe(filament());
});

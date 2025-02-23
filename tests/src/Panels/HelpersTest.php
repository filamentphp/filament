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

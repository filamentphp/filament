<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;

uses(TestCase::class);

it('can register pages', function (): void {
    expect(Filament::getPages())
        ->toContain(Settings::class);
});

<?php

use Filament\Facades\Filament;
use Filament\Tests\App\Fixtures\Pages\Settings;
use Filament\Tests\App\Pages\TestCase;

uses(TestCase::class);

it('can register pages', function () {
    expect(Filament::getPages())
        ->toContain(Settings::class);
});

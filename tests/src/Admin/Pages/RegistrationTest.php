<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Pages\TestCase;

uses(TestCase::class);

it('can register pages', function () {
    expect(Filament::getPages())
        ->toContain(Settings::class);
});

<?php

use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Pages\TestCase;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Settings::getUrl())->assertSuccessful();
});

it('can generate a slug based on the page name', function () {
    expect(Settings::getSlug())
        ->toBe('settings');
});

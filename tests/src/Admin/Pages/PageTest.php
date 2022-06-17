<?php

use Filament\Pages\Actions\Action;
use Filament\Tests\Admin\Fixtures\Pages\Settings;
use Filament\Tests\Admin\Pages\TestCase;
use Livewire\Livewire;

use function Livewire\invade;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Settings::getUrl())->assertSuccessful();
});

it('can generate a slug based on the page name', function () {
    expect(Settings::getSlug())
        ->toBe('settings');
});

it('can have default page actions', function () {
    Settings::defaultActions($actions = [
        Action::make('test')
    ]);

    expect(Settings::getDefaultActions())
        ->toBe($actions);

    expect(invade(app(Settings::class))->getActions())
        ->toBe($actions);
});

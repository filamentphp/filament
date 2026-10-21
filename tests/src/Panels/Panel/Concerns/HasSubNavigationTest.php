<?php

use Filament\Facades\Filament;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('uses the default sub-navigation width', function (): void {
    expect(Panel::make()->getSubNavigationWidth())->toBe('18rem');
});

it('can configure the sub-navigation width using `subNavigationWidth()`', function (string $width): void {
    $panel = Panel::make();

    expect($panel->subNavigationWidth($width))->toBe($panel);
    expect($panel->getSubNavigationWidth())->toBe($width);
})->with(['20rem', 'max-content']);

it('can evaluate a `Closure` for the sub-navigation width', function (): void {
    $width = '20rem';
    $panel = Panel::make()->subNavigationWidth(static function () use (&$width): string {
        return $width;
    });

    expect($panel->getSubNavigationWidth())->toBe('20rem');

    $width = 'max-content';

    expect($panel->getSubNavigationWidth())->toBe('max-content');
});

it('gets the sub-navigation width from the current panel', function (): void {
    $panel = Filament::getPanel('admin')->subNavigationWidth('max-content');

    Filament::setCurrentPanel($panel);

    expect(Filament::getSubNavigationWidth())->toBe('max-content');

    Filament::setCurrentPanel(Filament::getPanel('tenancy')->subNavigationWidth('24rem'));

    expect(Filament::getSubNavigationWidth())->toBe('24rem');
});

it('renders the configured sub-navigation width', function (SubNavigationPosition $position): void {
    $this->actingAs(User::factory()->create());

    Filament::getCurrentOrDefaultPanel()
        ->subNavigationPosition($position)
        ->subNavigationWidth('max-content');

    $this->get(ManageAdmins::getUrl())
        ->assertSuccessful()
        ->assertSee('--sub-navigation-width: max-content;', escape: false);
})->with([SubNavigationPosition::Start, SubNavigationPosition::End]);

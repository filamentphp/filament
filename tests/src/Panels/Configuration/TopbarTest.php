<?php

use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Enums\GlobalSearchPosition;
use Filament\Enums\UserMenuPosition;
use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('enables the topbar by default', function (): void {
    $panel = Panel::make();

    expect($panel->hasTopbar())->toBeTrue();
    expect($panel->hasTopbarOnMobile())->toBeTrue();
});

it('returns `false` for `hasTopbarOnMobile()` when the topbar is disabled', function (): void {
    $panel = Panel::make()->topbar(false);

    expect($panel->hasTopbar())->toBeFalse();
    expect($panel->hasTopbarOnMobile())->toBeFalse();
});

it('can use `topbarOnMobile()` to render a topbar on mobile when the topbar is disabled', function (): void {
    $panel = Panel::make()
        ->topbar(false)
        ->topbarOnMobile();

    expect($panel->hasTopbar())->toBeFalse();
    expect($panel->hasTopbarOnMobile())->toBeTrue();
});

it('can undo `topbarOnMobile()` by passing `false`', function (): void {
    $panel = Panel::make()
        ->topbar(false)
        ->topbarOnMobile()
        ->topbarOnMobile(false);

    expect($panel->hasTopbarOnMobile())->toBeFalse();
});

it('can use a `Closure` to control `topbarOnMobile()`', function (): void {
    $panel = Panel::make()
        ->topbar(false)
        ->topbarOnMobile(fn (): bool => true);

    expect($panel->hasTopbarOnMobile())->toBeTrue();
});

it('keeps the sidebar positions for global search, database notifications and the user menu when using `topbarOnMobile()`', function (): void {
    $panel = Panel::make()
        ->topbar(false)
        ->topbarOnMobile();

    expect($panel->getGlobalSearchPosition())->toBe(GlobalSearchPosition::Sidebar);
    expect($panel->getDatabaseNotificationsPosition())->toBe(DatabaseNotificationsPosition::Sidebar);
    expect($panel->getUserMenuPosition())->toBe(UserMenuPosition::Sidebar);
});

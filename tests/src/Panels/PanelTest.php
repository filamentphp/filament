<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\IconManager;
use Filament\Support\View\ViewManager;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;

uses(TestCase::class);

it('can call `bootTenancy()` repeatedly while preserving associations for multiple resources of the same model', function (): void {
    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
    $tenant = Team::factory()->create();
    Filament::setTenant($tenant);

    $panel->bootTenancy();
    $panel->bootTenancy();

    $user = User::factory()->create();

    expect($tenant->users()->where('user_id', $user->getKey())->count())->toBe(1)
        ->and($user->team_id)->toBe($tenant->getKey());
});

it('preserves the last configured icon across alternating compatible spellings', function (string $legacy, string $canonical): void {
    foreach ([[$legacy, $canonical], [$canonical, $legacy]] as [$first, $second]) {
        FilamentIcon::swap(new IconManager);

        $panel = Panel::make()->id('icon-compatibility')
            ->icons([$first => 'heroicon-o-check', $second => 'heroicon-o-star'])
            ->icons([$first => 'heroicon-o-x-mark']);

        expect(FilamentIcon::resolve([$first, $second]))->toBeNull();

        $panel->boot();

        expect(FilamentIcon::resolve($first))->toBe('heroicon-o-x-mark')
            ->and(FilamentIcon::resolve($second))->toBe('heroicon-o-x-mark');
    }
})->with([
    ['forms:components.rich-editor.toolbar.clear_formatting', 'forms::components.rich-editor.toolbar.clear-formatting'],
    ['schema::components.callout.info', 'schemas::components.callout.info'],
]);

it('preserves hook order and callback identity across alternating compatible spellings', function (string $legacy, string $canonical): void {
    foreach ([[$legacy, $canonical], [$canonical, $legacy]] as [$first, $second]) {
        FilamentView::swap(new ViewManager);
        $sharedHook = static fn (): string => 'shared';

        $panel = Panel::make()->id('hook-compatibility')
            ->renderHook($first, static fn (): string => 'A', scopes: 'page')
            ->renderHook($second, static fn (): string => 'B', scopes: 'page')
            ->renderHook($first, static fn (): string => 'C', scopes: 'page')
            ->renderHook($second, $sharedHook, scopes: ['page', 'resource'])
            ->renderHook($first, $sharedHook, scopes: 'page')
            ->renderHook($second, static fn (): string => 'global');

        expect(FilamentView::hasRenderHook($first, scopes: 'page'))->toBeFalse();

        $panel->boot();

        foreach ([$first, $second] as $name) {
            expect(FilamentView::hasRenderHook($name))->toBeTrue()
                ->and(FilamentView::renderHook($name, scopes: ['page', 'resource'])->toHtml())->toBe('globalABCshared');
        }
    }
})->with([
    ['panels::page.sub-navigation.module-menu.before', 'panels::page.sub-navigation.mobile-menu.before'],
    ['panels::page.sub-navigation.module-menu.after', 'panels::page.sub-navigation.mobile-menu.after'],
]);

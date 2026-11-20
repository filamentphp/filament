<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
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

it('preserves stateless panel render hook registrations without duplicating matching scopes', function (array $scopes): void {
    FilamentView::swap(new ViewManager);
    $panel = Panel::make()->id('independent-hooks');

    foreach (range(1, 2) as $registration) {
        $panel->renderHook('independent', static fn (): string => 'hook', scopes: $scopes);
    }

    $panel->boot();

    expect(FilamentView::renderHook('independent', scopes: ['resource', 'page', 'resource'])->toHtml())->toBe('hookhook');
})->with([
    'multiple scopes' => [['page', 'resource']],
    'global and repeated scopes' => [['', 'page', 'resource', 'page']],
]);

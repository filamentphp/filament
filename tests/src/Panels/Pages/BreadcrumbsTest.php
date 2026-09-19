<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster\Pages\ClusteredPageWithoutSubNavigation;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\Posts\Pages\ListPosts;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Panels\Pages\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

afterEach(function (): void {
    PostResource::navigationGroup('Blog');
    PostResource::navigationParentItem(null);
});

it('can configure strict hierarchical breadcrumbs with a `Closure`', function (): void {
    $panel = Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(false, strictHierarchical: fn (): bool => true);

    expect($panel->hasBreadcrumbs())->toBeFalse()
        ->and($panel->hasStrictHierarchicalBreadcrumbs())->toBeTrue();
});

it('preserves existing page and resource breadcrumbs when strict hierarchical breadcrumbs are disabled', function (): void {
    expect(app(Settings::class)->getBreadcrumbs())->toBe([])
        ->and(app(ListPosts::class)->getResourceBreadcrumbs())->toBe([
            PostResource::getUrl() => 'Posts',
        ]);
});

it('builds strict hierarchical breadcrumbs for a grouped page with a parent item', function (): void {
    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(strictHierarchical: true)
        ->navigationItems([
            NavigationItem::make('Administration home')
                ->group('Administration')
                ->icon(Heroicon::OutlinedHome)
                ->url('/administration'),
            ...GroupedBreadcrumbsPage::getNavigationItems(),
        ]);

    expect(app(GroupedBreadcrumbsPage::class)->getBreadcrumbs())->toBe([
        'Administration',
        '/administration' => 'Administration home',
        'Reports',
    ]);

    livewire(GroupedBreadcrumbsPage::class)
        ->assertSuccessful();
});

it('builds strict hierarchical breadcrumbs for a grouped resource page with a parent item', function (): void {
    PostResource::navigationParentItem('Blog home');

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(strictHierarchical: true)
        ->navigationItems([
            NavigationItem::make('Blog home')
                ->group('Blog')
                ->url('/blog'),
        ]);

    expect(app(ListPosts::class)->getResourceBreadcrumbs())->toBe([
        'Blog',
        '/blog' => 'Blog home',
        PostResource::getUrl() => 'Posts',
    ]);
});

it('builds strict hierarchical breadcrumbs for clustered pages', function (): void {
    Filament::getCurrentOrDefaultPanel()->breadcrumbs(strictHierarchical: true);

    expect(app(ManageAdmins::class)->getBreadcrumbs())->toBe([
        UserManagement::getUrl() => 'User Management',
        'User Management',
        'Manage Admins',
    ]);
});

it('falls back to cluster breadcrumbs when cluster sub-navigation is disabled', function (): void {
    Filament::getCurrentOrDefaultPanel()->breadcrumbs(strictHierarchical: true);

    expect(app(ClusteredPageWithoutSubNavigation::class)->getBreadcrumbs())->toBe([
        WithoutSubNavigationCluster::getUrl() => 'Without Sub Navigation',
        'Clustered Page Without Sub Navigation',
    ]);

    $this->get(ClusteredPageWithoutSubNavigation::getUrl())
        ->assertSuccessful();
});

it('falls back to existing breadcrumbs when a custom `NavigationBuilder` omits the page hierarchy', function (): void {
    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(strictHierarchical: true)
        ->navigation(fn (NavigationBuilder $navigation): NavigationBuilder => $navigation);

    expect(app(GroupedBreadcrumbsPage::class)->getBreadcrumbs())->toBe(['Reports']);
});

it('falls back to existing breadcrumbs for a grouped page that does not register navigation', function (): void {
    Filament::getCurrentOrDefaultPanel()->breadcrumbs(strictHierarchical: true);

    expect(app(GroupedUnregisteredBreadcrumbsPage::class)->getBreadcrumbs())->toBe([
        'Archived reports',
    ]);
});

class GroupedBreadcrumbsPage extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Administration';

    protected static ?string $navigationParentItem = 'Administration home';

    protected static ?string $title = 'Reports';

    protected string $view = 'filament-panels::pages.page';

    public static function getNavigationUrl(): string
    {
        return '/reports';
    }
}

class GroupedUnregisteredBreadcrumbsPage extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Administration';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Archived reports';

    protected string $view = 'filament-panels::pages.page';
}

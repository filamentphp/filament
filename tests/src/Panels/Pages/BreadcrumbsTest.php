<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Resources\Pages\Page as ResourcePage;
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
    UserManagement::navigationGroup(null);
    UserManagement::navigationParentItem(null);
    WithoutSubNavigationCluster::navigationGroup(null);
});

it('can configure the navigation hierarchy in breadcrumbs with a `Closure`', function (): void {
    $panel = Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(false, hasNavigationHierarchy: fn (): bool => true);

    expect($panel->hasBreadcrumbs())->toBeFalse()
        ->and($panel->hasNavigationHierarchyInBreadcrumbs())->toBeTrue();
});

it('preserves existing page and resource breadcrumbs when the navigation hierarchy is not included', function (): void {
    expect(app(Settings::class)->getBreadcrumbs())->toBe([])
        ->and(app(ListPosts::class)->getResourceBreadcrumbs())->toBe([
            PostResource::getUrl() => 'Posts',
        ]);
});

it('includes an explicitly configured custom page breadcrumb without the navigation hierarchy', function (): void {
    expect(app(PageWithBreadcrumb::class)->getBreadcrumbs())->toBe([
        'Custom breadcrumb',
    ]);
});

it('preserves the title fallback for resource page breadcrumbs', function (): void {
    expect(app(ResourcePageWithTitle::class)->getBreadcrumb())->toBe('Resource page title');
});

it('includes the navigation hierarchy in breadcrumbs for a grouped page with a parent item', function (): void {
    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
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

it('includes the navigation hierarchy in breadcrumbs for a grouped resource page with a parent item', function (): void {
    PostResource::navigationParentItem('Blog home');

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigationItems([
            NavigationItem::make('Blog home')
                ->group('Blog')
                ->icon(Heroicon::OutlinedHome)
                ->url('/blog'),
        ]);

    expect(app(ListPosts::class)->getResourceBreadcrumbs())->toBe([
        'Blog',
        '/blog' => 'Blog home',
        PostResource::getUrl() => 'Posts',
    ]);
});

it('renders the navigation hierarchy in breadcrumbs', function (): void {
    PostResource::navigationParentItem('Blog home');

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigationItems([
            NavigationItem::make('Blog home')
                ->group('Blog')
                ->icon(Heroicon::OutlinedHome)
                ->url('/blog'),
        ]);

    visit(PostResource::getUrl())
        ->assertScript(<<<'JS'
            Array.from(document.querySelectorAll('.fi-breadcrumbs-item-label')).map((item) => [
                item.textContent.trim(),
                item.href ? new URL(item.href).pathname : null,
            ])
            JS, [
            ['Blog', null],
            ['Blog home', '/blog'],
            ['Posts', '/posts'],
            ['List', null],
        ])
        ->assertNoAccessibilityIssues();

    visit(PostResource::getUrl())
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('includes the navigation hierarchy in breadcrumbs for clustered pages', function (): void {
    UserManagement::navigationGroup('Administration');

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true);

    expect(app(ManageAdmins::class)->getBreadcrumbs())->toBe([
        'Administration',
        UserManagement::getUrl() => 'User Management',
        'User Management',
        'Manage Admins',
    ]);
});

it('includes the navigation hierarchy in breadcrumbs for clustered resource record pages with sub-navigation', function (): void {
    UserManagement::navigationGroup('Administration');

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true);

    expect(app(ClusteredResourceRecordPage::class)->getBreadcrumbs())->toBe([
        'Administration',
        UserManagement::getUrl() => 'User Management',
        'Record',
        PostResource::getUrl() => 'Posts',
        'Edit post',
    ]);
});

it('falls back to cluster breadcrumbs when cluster sub-navigation is disabled', function (): void {
    WithoutSubNavigationCluster::navigationGroup('Administration');

    $breadcrumbs = app(ClusteredPageWithoutSubNavigation::class)->getBreadcrumbs();

    Filament::getCurrentOrDefaultPanel()->breadcrumbs(hasNavigationHierarchy: true);

    expect(app(ClusteredPageWithoutSubNavigation::class)->getBreadcrumbs())->toBe($breadcrumbs);

    $this->get(ClusteredPageWithoutSubNavigation::getUrl())
        ->assertSuccessful();
});

it('falls back to cluster breadcrumbs when a custom `NavigationBuilder` omits the cluster', function (): void {
    $breadcrumbs = app(ManageAdmins::class)->getBreadcrumbs();

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigation(fn (NavigationBuilder $navigation): NavigationBuilder => $navigation);

    expect(app(ManageAdmins::class)->getBreadcrumbs())->toBe($breadcrumbs);
});

it('falls back to existing breadcrumbs when a custom `NavigationBuilder` omits the page hierarchy', function (): void {
    $breadcrumbs = app(GroupedBreadcrumbsPage::class)->getBreadcrumbs();

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigation(fn (NavigationBuilder $navigation): NavigationBuilder => $navigation);

    expect(app(GroupedBreadcrumbsPage::class)->getBreadcrumbs())->toBe($breadcrumbs);
});

it('includes the navigation hierarchy in breadcrumbs from a custom `NavigationBuilder`', function (): void {
    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigation(fn (NavigationBuilder $navigation): NavigationBuilder => $navigation
            ->group(NavigationGroup::make('Administration')
                ->items(GroupedBreadcrumbsPage::getNavigationItems())));

    expect(app(GroupedBreadcrumbsPage::class)->getBreadcrumbs())->toBe([
        'Administration',
        'Reports',
    ]);
});

it('falls back to existing breadcrumbs for a grouped page that does not register navigation', function (): void {
    $breadcrumbs = app(GroupedUnregisteredBreadcrumbsPage::class)->getBreadcrumbs();

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigationItems([
            NavigationItem::make('Published reports')
                ->key(GroupedUnregisteredBreadcrumbsPage::class)
                ->group('Administration')
                ->icon(Heroicon::OutlinedDocumentText)
                ->childItems([
                    NavigationItem::make('Published report')
                        ->url('/published-reports'),
                ]),
        ]);

    expect(app(GroupedUnregisteredBreadcrumbsPage::class)->getBreadcrumbs())->toBe($breadcrumbs);
});

it('falls back to existing breadcrumbs when navigation is disabled', function (): void {
    $breadcrumbs = app(GroupedBreadcrumbsPage::class)->getBreadcrumbs();

    Filament::getCurrentOrDefaultPanel()
        ->breadcrumbs(hasNavigationHierarchy: true)
        ->navigationItems([
            NavigationItem::make('Administration home')
                ->group('Administration')
                ->icon(Heroicon::OutlinedHome)
                ->url('/administration'),
            ...GroupedBreadcrumbsPage::getNavigationItems(),
        ])
        ->navigation(false);

    expect(app(GroupedBreadcrumbsPage::class)->getBreadcrumbs())->toBe($breadcrumbs);
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

class PageWithBreadcrumb extends Page
{
    protected static ?string $breadcrumb = 'Custom breadcrumb';

    protected string $view = 'filament-panels::pages.page';
}

class ResourcePageWithTitle extends ResourcePage
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Resource page title';
}

class ClusteredResourceRecordPage extends ResourcePage
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Edit post';

    public static function getCluster(): ?string
    {
        return UserManagement::class;
    }

    public function getSubNavigation(): array
    {
        return [
            NavigationItem::make('Edit post')
                ->key(static::class)
                ->group('Record')
                ->url('/posts/1/edit'),
        ];
    }

    public function getSubNavigationParameters(): array
    {
        return ['record' => 1];
    }

    public static function getNavigationUrl(array $parameters = []): string
    {
        return "/posts/{$parameters['record']}/edit";
    }
}

class GroupedUnregisteredBreadcrumbsPage extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Administration';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Archived reports';

    protected string $view = 'filament-panels::pages.page';
}

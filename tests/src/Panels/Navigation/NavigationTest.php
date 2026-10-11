<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Contracts\Collapsible;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster\Pages\ClusteredPageWithoutSubNavigation;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\Navigation\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('supports `Htmlable` navigation labels and closures', function (): void {
    $label = new class implements Htmlable
    {
        public function toHtml(): string
        {
            return '<strong>Reports &amp; billing</strong>';
        }
    };

    expect(NavigationGroup::make($label)->getLabel())->toBe($label)
        ->and(NavigationItem::make($label)->getLabel())->toBe($label)
        ->and(NavigationGroup::make()->label(static fn (): Htmlable => $label)->getLabel())->toBe($label)
        ->and(NavigationItem::make()->label(static fn (): Htmlable => $label)->getLabel())->toBe($label)
        ->and(NavigationItem::make($label)->getKey())->toBe('<strong>Reports &amp; billing</strong>');
});

it('matches and orders `Htmlable` navigation group labels and parents', function (): void {
    $label = new HtmlString('<strong>Reports</strong>');

    Filament::getCurrentOrDefaultPanel()
        ->navigationGroups([
            NavigationGroup::make($label),
        ])
        ->navigationItems([
            NavigationItem::make($label)->key('reports')->group($label->toHtml())->url('/reports'),
            NavigationItem::make('Invoices')->parentItem($label->toHtml())->group($label->toHtml())->url('/invoices'),
        ]);

    $groups = array_values(Filament::getNavigation());

    expect($groups[1]->getLabel())->toBe($label)
        ->and(collect($groups[1]->getItems())->first()->getChildItems()[0]->getLabel())->toBe('Invoices');
});

it('supports `Htmlable` group and parent labels in sub-navigation', function (): void {
    $page = new class extends Page
    {
        public function getSubNavigation(): array
        {
            return [
                NavigationGroup::make(new HtmlString('<strong>Reports</strong>')),
                NavigationItem::make(new HtmlString('<strong>Overview</strong>'))->key('overview')->group('<strong>Reports</strong>')->url('/overview'),
                NavigationItem::make('Invoices')->parentItem('<strong>Overview</strong>')->group('<strong>Reports</strong>')->url('/invoices'),
            ];
        }
    };

    $group = array_values($page->getCachedSubNavigation())[0];

    expect($group->getLabel())->toBeInstanceOf(Htmlable::class)
        ->and($group->getLabel()->toHtml())->toBe('<strong>Reports</strong>')
        ->and(collect($group->getItems())->first()->getChildItems()[0]->getLabel())->toBe('Invoices');
});

it('renders `Htmlable` navigation labels and tooltips while escaping string labels', function (): void {
    Artisan::call('filament:assets');

    Filament::getCurrentOrDefaultPanel()
        ->sidebarCollapsibleOnDesktop()
        ->navigationGroups([
            'literal' => NavigationGroup::make('<b>Archives &amp; billing</b>')
                ->icon(Heroicon::OutlinedFolder)
                ->extraSidebarAttributes(['data-testid' => 'literal-group']),
            'rich' => NavigationGroup::make(new HtmlString('<b>Reports &amp; &lt;Draft&gt;</b>'))
                ->icon(Heroicon::OutlinedFolder)
                ->extraSidebarAttributes(['data-testid' => 'rich-group']),
        ])
        ->navigationItems([
            NavigationItem::make('<b>Overview &amp; insights</b>')
                ->icon(Heroicon::OutlinedChartBar)
                ->url(Settings::getUrl())
                ->extraAttributes(['data-testid' => 'literal-item']),
            NavigationItem::make(new HtmlString('<b>Overview &amp; insights</b>'))
                ->icon(Heroicon::OutlinedChartBar)
                ->url(Settings::getUrl())
                ->extraAttributes(['data-testid' => 'rich-item']),
            NavigationItem::make('Invoices')->group('literal')->url(Settings::getUrl()),
            NavigationItem::make('Reports')->group('rich')->url(Settings::getUrl()),
        ]);

    $page = visit(Settings::getUrl())
        ->inDarkMode()
        ->assertSeeIn('[data-testid="literal-item"]', '<b>Overview &amp; insights</b>')
        ->assertMissing('[data-testid="literal-item"] b')
        ->assertSeeIn('[data-testid="rich-item"] b', 'Overview & insights')
        ->assertSeeIn('[data-testid="literal-group"] > div > span:visible', '<b>Archives &amp; billing</b>')
        ->assertMissing('[data-testid="literal-group"] b')
        ->assertSeeIn('[data-testid="rich-group"] > div b:visible', 'Reports & <Draft>')
        ->assertAttribute('[data-testid="rich-group"] button[aria-controls]:not([aria-haspopup])', 'aria-label', 'Reports & <Draft>')
        ->assertNoAccessibilityIssues();

    $page->script("window.dispatchEvent(new CustomEvent('theme-changed', { detail: 'light' }))");
    $page->assertNoAccessibilityIssues()
        ->click('button[aria-label="' . __('filament-panels::layout.actions.sidebar.collapse.label') . '"]:visible')
        ->hover('[data-testid="literal-item"] a')
        ->assertSeeIn('[role="tooltip"]', '<b>Overview &amp; insights</b>')
        ->assertMissing('[role="tooltip"] b')
        ->hover('[data-testid="rich-item"] a')
        ->assertSeeIn('[role="tooltip"] b', 'Overview & insights')
        ->hover('[data-testid="literal-group"] button[aria-haspopup="menu"]')
        ->assertSeeIn('[role="tooltip"]', '<b>Archives &amp; billing</b>')
        ->assertMissing('[role="tooltip"] b')
        ->hover('[data-testid="rich-group"] button[aria-haspopup="menu"]')
        ->assertSeeIn('[role="tooltip"] b', 'Reports & <Draft>')
        ->assertAttribute('[data-testid="rich-group"] button[aria-haspopup="menu"]', 'aria-label', 'Reports & <Draft>');
});

describe('registration and ordering', function (): void {
    it('can register navigation items from resources and pages', function (): void {
        $navigation = Filament::getNavigation();
        $groups = collect($navigation);

        expect($groups)->each->toBeInstanceOf(NavigationGroup::class);

        $allItems = $groups->flatMap(fn (NavigationGroup $group) => $group->getItems());
        expect($allItems)->each->toBeInstanceOf(NavigationItem::class);

        $itemLabels = $allItems->map(fn (NavigationItem $item) => $item->getLabel());

        // Verify core pages and resources are registered
        expect($itemLabels)
            ->toContain('Dashboard')
            ->toContain('Companies')
            ->toContain('Users')
            ->toContain('Posts')
            ->toContain('Products');

        // Verify navigation groups contain expected items
        $groupLabels = $groups->map(fn (NavigationGroup $group) => $group->getLabel());
        expect($groupLabels)->toContain(null)->toContain('Blog')->toContain('Shop');

        $blogItemLabels = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === 'Blog')->getItems())
            ->map(fn (NavigationItem $item) => $item->getLabel());
        expect($blogItemLabels)->toContain('Posts')->toContain('Post Categories');

        $shopItemLabels = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === 'Shop')->getItems())
            ->map(fn (NavigationItem $item) => $item->getLabel());
        expect($shopItemLabels)->toContain('Products');

        // Verify Dashboard appears first (has lowest sort order)
        $defaultGroupItems = collect($groups->first(fn (NavigationGroup $group) => $group->getLabel() === null)->getItems());
        expect($defaultGroupItems->first()->getLabel())->toBe('Dashboard');
    });

    it('can reorder navigation groups by registering them', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationGroups([
            NavigationGroup::make()->label('Shop'),
            NavigationGroup::make()->label('Blog'),
        ]);

        expect(Filament::getNavigation())
            ->sequence(
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBeNull(),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Shop'),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Blog'),
            );
    });

    it('can reorder navigation groups by registering them with different labels', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationGroups([
            'Shop' => NavigationGroup::make()->label('Store'),
            'Blog' => NavigationGroup::make()->label('Posts'),
        ]);

        expect(Filament::getNavigation())
            ->sequence(
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBeNull(),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Store'),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Posts'),
            );
    });

    it('can reorder navigation groups by registering their labels', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationGroups([
            'Shop',
            'Blog',
        ]);

        expect(Filament::getNavigation())
            ->sequence(
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBeNull(),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Shop'),
                fn ($group) => $group
                    ->toBeInstanceOf(NavigationGroup::class)
                    ->getLabel()->toBe('Blog'),
            );
    });

    it('orders several navigation groups by their registration order', function (): void {
        // `Shop` and `Blog` already contain resource items; `Reports` is given
        // its own item so it survives the empty-group filter.
        Filament::getCurrentOrDefaultPanel()
            ->navigationGroups([
                NavigationGroup::make()->label('Reports'),
                NavigationGroup::make()->label('Shop'),
                NavigationGroup::make()->label('Blog'),
            ])
            ->navigationItems([
                NavigationItem::make('Sales')
                    ->group('Reports')
                    ->url('#'),
            ]);

        $groupLabels = collect(Filament::getNavigation())
            ->map(fn (NavigationGroup $group): ?string => $group->getLabel())
            ->values()
            ->all();

        expect($groupLabels)->toBe([null, 'Reports', 'Shop', 'Blog']);
    });

    it('orders navigation groups registered from a `UnitEnum` by their `cases()` order', function (): void {
        // Items are registered in reverse `cases()` order to prove the sort
        // follows enum order, not registration order.
        Filament::getCurrentOrDefaultPanel()
            ->navigationGroups(NavigationGroupEnum::class)
            ->navigationItems([
                NavigationItem::make('Manage Settings')
                    ->group(NavigationGroupEnum::Settings)
                    ->url('#'),
                NavigationItem::make('Manage Users')
                    ->group(NavigationGroupEnum::Users)
                    ->url('#'),
            ]);

        $groupLabels = collect(Filament::getNavigation())
            ->map(fn (NavigationGroup $group): ?string => $group->getLabel())
            ->values()
            ->all();

        $usersPosition = array_search('User Management', $groupLabels);
        $settingsPosition = array_search('System Settings', $groupLabels);

        expect($usersPosition)->not->toBeFalse();
        expect($settingsPosition)->not->toBeFalse();
        expect($usersPosition)->toBeLessThan($settingsPosition);
    });
});

describe('navigation parent-child', function (): void {
    it('can group a navigation item under a resource\'s default item by referencing the resource class name', function (): void {
        // `UserResource`'s default navigation item is automatically keyed
        // with `->key(static::class)`, so it can now be targeted as a parent
        // by class name alone, without needing to match its label.
        Filament::getCurrentOrDefaultPanel()->navigationItems([
            NavigationItem::make('Impersonations')
                ->parentItem(UserResource::class)
                ->url('#'),
        ]);

        $allItems = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems());

        $users = $allItems->first(fn (NavigationItem $item) => $item->getLabel() === 'Users');

        expect($users)->not->toBeNull();

        $childLabels = $users->getChildItems()
            ->map(fn (NavigationItem $item) => $item->getLabel())
            ->all();

        expect($childLabels)->toContain('Impersonations');
    });

    it('merges navigation items referencing the same parent by both class name and label', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationItems([
            NavigationItem::make('Impersonations')
                ->parentItem(UserResource::class) // by key (class name)
                ->url('#'),
            NavigationItem::make('Login History')
                ->parentItem('Users') // by label
                ->url('#'),
        ]);

        $allItems = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems());

        $users = $allItems->first(fn (NavigationItem $item) => $item->getLabel() === 'Users');

        $childLabels = $users->getChildItems()
            ->map(fn (NavigationItem $item) => $item->getLabel())
            ->all();

        expect($childLabels)->toContain('Impersonations');
        expect($childLabels)->toContain('Login History');
    });

    it('does not duplicate merged navigation children when the navigation is retrieved more than once', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationItems([
            NavigationItem::make('Impersonations')
                ->parentItem(UserResource::class)
                ->url('#'),
            NavigationItem::make('Login History')
                ->parentItem('Users')
                ->url('#'),
        ]);

        // `NavigationManager` is a scoped (per-request) singleton, so `get()`
        // must be idempotent — retrieving the navigation programmatically
        // before it is rendered previously duplicated the merged children.
        Filament::getNavigation();

        $allItems = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems());

        $users = $allItems->first(fn (NavigationItem $item) => $item->getLabel() === 'Users');

        $childLabels = $users->getChildItems()
            ->map(fn (NavigationItem $item) => $item->getLabel())
            ->all();

        expect($childLabels)->toHaveCount(2);
        expect($childLabels)->toContain('Impersonations');
        expect($childLabels)->toContain('Login History');
    });

    it('sorts merged navigation children by their sort order', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationItems([
            NavigationItem::make('Impersonations')
                ->parentItem(UserResource::class)
                ->sort(2)
                ->url('#'),
            NavigationItem::make('Login History')
                ->parentItem('Users')
                ->sort(1)
                ->url('#'),
        ]);

        $allItems = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems());

        $users = $allItems->first(fn (NavigationItem $item) => $item->getLabel() === 'Users');

        $childLabels = $users->getChildItems()
            ->map(fn (NavigationItem $item) => $item->getLabel())
            ->values()
            ->all();

        expect($childLabels)->toBe(['Login History', 'Impersonations']);
    });

    it('drops a navigation item whose parent matches neither an existing key nor an existing label', function (): void {
        Filament::getCurrentOrDefaultPanel()->navigationItems([
            NavigationItem::make('Impersonations')
                ->parentItem('App\\Filament\\Resources\\SomeOtherResource')
                ->url('#'),
        ]);

        $allItems = collect(Filament::getNavigation())
            ->flatMap(fn (NavigationGroup $group) => $group->getItems());

        $itemLabels = $allItems->map(fn (NavigationItem $item) => $item->getLabel());

        expect($itemLabels)->not->toContain('Impersonations');

        $users = $allItems->first(fn (NavigationItem $item) => $item->getLabel() === 'Users');

        expect($users->getChildItems())->toBeEmpty();
    });
});

describe('navigation groups from enums', function (): void {
    it('can use enum `Collapsible` to make a group collapsible but not collapsed', function (): void {
        $group = NavigationGroup::fromEnum(CollapsibleNavigationGroupEnum::Group);

        expect($group->isCollapsible())->toBeTrue();
        expect($group->isCollapsed())->toBeFalse();
    });
});

describe('navigation item key', function (): void {
    it('falls back to the label when no key is set', function (): void {
        $item = NavigationItem::make('Settings');

        expect($item->getKey())->toBe('Settings');
    });

    it('uses the explicit key instead of the label when one is set', function (): void {
        $item = NavigationItem::make('Settings')
            ->key('settings-page-key');

        expect($item->getKey())->toBe('settings-page-key');
    });

    it('gives a resource\'s default navigation item a key matching the resource class', function (): void {
        $items = UserResource::getNavigationItems();

        expect($items)->toHaveCount(1);
        expect($items[0]->getKey())->toBe(UserResource::class);
    });
});

describe('sub-navigation parent-child', function (): void {
    it('can establish parent-child relationships in sub-navigation', function (): void {
        // Create a test class that uses the `HasSubNavigation` trait
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->url('/users'),
                    NavigationItem::make('Products')
                        ->parentItem('Users')
                        ->url('/products'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();

        // Should have one group
        expect($navigation)->toHaveCount(1);

        $group = $navigation[0];
        $items = $group->getItems();

        // Should only have the parent item at the top level (child is nested)
        expect($items)->toHaveCount(1);
        expect($items[0]->getLabel())->toBe('Users');

        // Parent should have the child item
        $childItems = $items[0]->getChildItems();
        expect($childItems)->toHaveCount(1);
        expect($childItems->first()->getLabel())->toBe('Products');
    });

    it('keeps parent items without children in sub-navigation', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Settings')
                        ->url('/settings'),
                    NavigationItem::make('Users')
                        ->url('/users'),
                    NavigationItem::make('Products')
                        ->parentItem('Users')
                        ->url('/products'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();

        $group = $navigation[0];
        $items = collect($group->getItems());

        // Should have two top-level items: Settings and Users
        expect($items)->toHaveCount(2);

        $settings = $items->first(fn ($i) => $i->getLabel() === 'Settings');
        $users = $items->first(fn ($i) => $i->getLabel() === 'Users');

        expect($settings)->not()->toBeNull();
        expect($settings->getChildItems())->toBeEmpty();

        expect($users)->not()->toBeNull();
        expect($users->getChildItems())->toHaveCount(1);
        expect($users->getChildItems()->first()->getLabel())->toBe('Products');
    });

    it('handles child items with non-existent parent in sub-navigation', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Settings')
                        ->url('/settings'),
                    NavigationItem::make('Products')
                        ->parentItem('NonExistentParent')
                        ->url('/products'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();

        $group = $navigation[0];
        $items = collect($group->getItems());

        // Only Settings should appear (Products has non-existent parent and is dropped)
        expect($items)->toHaveCount(1);
        expect($items->first()->getLabel())->toBe('Settings');
    });

    it('establishes parent-child relationships within navigation groups', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationGroup::make('System'),
                    NavigationItem::make('Users')
                        ->group('System')
                        ->url('/users'),
                    NavigationItem::make('Roles')
                        ->group('System')
                        ->parentItem('Users')
                        ->url('/roles'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();

        // Find the System group
        $systemGroup = collect($navigation)->first(fn ($g) => $g->getLabel() === 'System');
        expect($systemGroup)->not()->toBeNull();

        $items = collect($systemGroup->getItems());

        // Should only have Users at top level
        expect($items)->toHaveCount(1);
        expect($items->first()->getLabel())->toBe('Users');

        // Users should have Roles as child
        $childItems = $items->first()->getChildItems();
        expect($childItems)->toHaveCount(1);
        expect($childItems->first()->getLabel())->toBe('Roles');
    });

    it('supports multiple children under one parent in sub-navigation', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->url('/users'),
                    NavigationItem::make('Roles')
                        ->parentItem('Users')
                        ->url('/roles'),
                    NavigationItem::make('Permissions')
                        ->parentItem('Users')
                        ->url('/permissions'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();

        $group = $navigation[0];
        $items = $group->getItems();

        // Should only have Users at top level
        expect($items)->toHaveCount(1);
        expect($items[0]->getLabel())->toBe('Users');

        // Users should have both Roles and Permissions as children
        $childItems = $items[0]->getChildItems();
        expect($childItems)->toHaveCount(2);

        $childLabels = $childItems->map(fn (NavigationItem $item) => $item->getLabel())->all();
        expect($childLabels)->toContain('Roles');
        expect($childLabels)->toContain('Permissions');
    });

    it('can reference a sub-navigation parent item by its key instead of its label', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->key(UserResource::class)
                        ->url('/users'),
                    NavigationItem::make('Products')
                        ->parentItem(UserResource::class)
                        ->url('/products'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();
        $items = $navigation[0]->getItems();

        expect($items)->toHaveCount(1);
        expect($items[0]->getLabel())->toBe('Users');

        $childItems = $items[0]->getChildItems();
        expect($childItems)->toHaveCount(1);
        expect($childItems->first()->getLabel())->toBe('Products');
    });

    it('merges sub-navigation child items onto a parent referenced by both key and label instead of overwriting them', function (): void {
        // "Roles" targets the parent by label ("Users"); "Permissions" targets
        // the same parent by its key. These land in two separate `groupBy()`
        // buckets, so their child items must be merged onto the parent rather
        // than the second bucket overwriting the first.
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->key(UserResource::class)
                        ->url('/users'),
                    NavigationItem::make('Roles')
                        ->parentItem('Users')
                        ->url('/roles'),
                    NavigationItem::make('Permissions')
                        ->parentItem(UserResource::class)
                        ->url('/permissions'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();
        $items = $navigation[0]->getItems();

        expect($items)->toHaveCount(1);

        $childItems = $items[0]->getChildItems();
        expect($childItems)->toHaveCount(2);

        $childLabels = $childItems->map(fn (NavigationItem $item) => $item->getLabel())->all();
        expect($childLabels)->toContain('Roles');
        expect($childLabels)->toContain('Permissions');
    });

    it('sorts merged sub-navigation child items by their sort order', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->key(UserResource::class)
                        ->url('/users'),
                    NavigationItem::make('Roles')
                        ->parentItem('Users')
                        ->sort(2)
                        ->url('/roles'),
                    NavigationItem::make('Permissions')
                        ->parentItem(UserResource::class)
                        ->sort(1)
                        ->url('/permissions'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();
        $childItems = $navigation[0]->getItems()[0]->getChildItems();

        expect($childItems->map(fn (NavigationItem $item) => $item->getLabel())->values()->all())
            ->toBe(['Permissions', 'Roles']);
    });

    it('still drops a sub-navigation child item whose parent matches neither an existing key nor an existing label', function (): void {
        $page = new class extends Page
        {
            protected string $view = 'filament-panels::pages.page';

            public function getSubNavigation(): array
            {
                return [
                    NavigationItem::make('Users')
                        ->key(UserResource::class)
                        ->url('/users'),
                    NavigationItem::make('Products')
                        ->parentItem('App\\Filament\\Resources\\SomeOtherResource')
                        ->url('/products'),
                ];
            }
        };

        $navigation = $page->getCachedSubNavigation();
        $items = collect($navigation[0]->getItems());

        expect($items)->toHaveCount(1);
        expect($items->first()->getLabel())->toBe('Users');
        expect($items->first()->getChildItems())->toBeEmpty();
    });
});

describe('cluster sub-navigation', function (): void {
    it('can use enum `HasLabel` for cluster sub-navigation groups', function (): void {
        // Access a page within the cluster
        $this->get(ManageAdmins::getUrl())->assertSuccessful();

        // Get the page instance to test sub-navigation (pages within cluster also have sub-navigation)
        $component = livewire(ManageAdmins::class);

        $subNavigation = $component->instance()->getCachedSubNavigation();

        // Should have groups with proper labels from `HasLabel` interface
        $groupLabels = collect($subNavigation)
            ->filter(fn (NavigationGroup $group) => filled($group->getLabel()))
            ->map(fn (NavigationGroup $group) => $group->getLabel())
            ->values()
            ->all();

        // The enum `NavigationGroupEnum` has `getLabel()` returning 'User Management' for Users
        // and 'System Settings' for Settings - NOT the raw enum name like 'Users' or 'Settings'
        expect($groupLabels)->toContain('User Management');
        expect($groupLabels)->toContain('System Settings');
        expect($groupLabels)->not->toContain('Users');
        expect($groupLabels)->not->toContain('Settings');
    });

    it('can disable sub-navigation for a cluster', function (): void {
        // Access a page within the cluster that has sub-navigation disabled
        $this->get(ClusteredPageWithoutSubNavigation::getUrl())->assertSuccessful();

        $component = livewire(ClusteredPageWithoutSubNavigation::class);

        $subNavigation = $component->instance()->getSubNavigation();

        expect($subNavigation)->toBe([]);
    });

    it('returns sub-navigation when cluster has sub-navigation enabled', function (): void {
        // `UserManagement` cluster has sub-navigation enabled by default
        $this->get(ManageAdmins::getUrl())->assertSuccessful();

        $component = livewire(ManageAdmins::class);

        $subNavigation = $component->instance()->getSubNavigation();

        expect($subNavigation)->not->toBeEmpty();
    });

    it('can disable sub-navigation for resource pages in a cluster', function (): void {
        // Create a test resource page that would be in a cluster with sub-navigation disabled
        $listRecordsPage = new class extends ListRecords
        {
            protected static ?string $cluster = WithoutSubNavigationCluster::class;

            protected static string $resource = UserResource::class;
        };

        $subNavigation = $listRecordsPage->getSubNavigation();

        expect($subNavigation)->toBeEmpty();
    });

    it('can check if cluster should register sub-navigation', function (): void {
        // Test that `UserManagement` cluster has sub-navigation enabled by default
        expect(UserManagement::shouldRegisterSubNavigation())->toBeTrue();

        // Test that `WithoutSubNavigationCluster` has sub-navigation disabled
        expect(WithoutSubNavigationCluster::shouldRegisterSubNavigation())->toBeFalse();
    });
});

enum CollapsibleNavigationGroupEnum implements Collapsible
{
    case Group;

    public function isCollapsible(): bool
    {
        return true;
    }

    public function isCollapsed(): bool
    {
        return false;
    }
}

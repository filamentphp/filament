<?php

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Livewire\Sidebar;
use Filament\Livewire\Topbar;
use Filament\Models\Contracts\HasName;
use Filament\Pages\Dashboard;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    actingAs(User::factory()->create());

    // Grant access so the injected `profile` and `register` items resolve as visible.
    Gate::before(fn (): bool => true);
});

it('preserves `Htmlable` tenant names and uses plain text for default avatars and user names', function (): void {
    $tenant = new class extends Team implements HasName
    {
        public function getFilamentName(): Htmlable
        {
            return new class implements Htmlable
            {
                public function toHtml(): string
                {
                    return '<strong>Research &amp; billing</strong>';
                }
            };
        }
    };

    Filament::getCurrentOrDefaultPanel()->tenant($tenant::class);

    expect(Filament::getTenantName($tenant))->toBeInstanceOf(Htmlable::class)
        ->and(Filament::getTenantName($tenant)->toHtml())->toBe('<strong>Research &amp; billing</strong>')
        ->and(Filament::getNameForDefaultAvatar($tenant))->toBe('Research & billing')
        ->and(Filament::getUserName($tenant))->toBe('Research & billing');
});

it('renders and searches `Htmlable` tenant names while escaping string names', function (): void {
    Artisan::call('filament:assets');
    $this->withoutMiddleware(Authenticate::class);

    Filament::setCurrentPanel('tenant-menu-flat');
    Filament::getCurrentPanel()->sidebarCollapsibleOnDesktop()->searchableTenantMenu();

    $tenant = Team::factory()->create(['name' => '<b>Research &amp; billing</b>']);
    $switchableTenant = Team::factory()->create(['name' => '<b>Support &amp; sales</b>']);
    $literalTenant = Team::factory()->create(['name' => '<b>Archives</b>']);

    Team::retrieved(static function (Team $team) use ($literalTenant): void {
        if (! $team->is($literalTenant)) {
            $team->name = new HtmlString($team->name);
        }
    });

    $page = visit(Dashboard::getUrl(panel: 'tenant-menu-flat', tenant: $tenant))->inDarkMode();
    $tenantTrigger = 'button:has(img[alt*="Research"])';

    $page->assertSeeIn("{$tenantTrigger} b", 'Research & billing');

    $page->script('async () => { await new Promise(requestAnimationFrame); await Promise.all(document.getAnimations().map((animation) => animation.finished)); }');
    $page->assertNoAccessibilityIssues();
    $page->script("window.dispatchEvent(new CustomEvent('theme-changed', { detail: 'light' }))");
    $page->script('async () => { await new Promise(requestAnimationFrame); await Promise.all(document.getAnimations().map((animation) => animation.finished)); }');
    $page->assertNoAccessibilityIssues();
    $page->click('button[aria-label="' . __('filament-panels::layout.actions.sidebar.collapse.label') . '"]:visible')
        ->hover($tenantTrigger)
        ->assertSeeIn('[role="tooltip"] b', 'Research & billing');

    $switchableTenantUrl = Filament::getUrl($switchableTenant);
    $literalTenantUrl = Filament::getUrl($literalTenant);

    $page->click($tenantTrigger)
        ->assertSeeIn("a[href=\"{$literalTenantUrl}\"]", '<b>Archives</b>')
        ->assertMissing("a[href=\"{$literalTenantUrl}\"] b")
        ->type('input[x-model="search"]', 'Support & sales')
        ->assertVisible("a[href=\"{$switchableTenantUrl}\"]")
        ->assertMissing("a[href=\"{$literalTenantUrl}\"]:visible")
        ->type('input[x-model="search"]', '<b>Archives</b>')
        ->assertVisible("a[href=\"{$literalTenantUrl}\"]")
        ->assertMissing("a[href=\"{$switchableTenantUrl}\"]:visible")
        ->click("a[href=\"{$literalTenantUrl}\"]")
        ->hover('button:has(img[alt*="Archives"])')
        ->assertSeeIn('[role="tooltip"]', '<b>Archives</b>')
        ->assertMissing('[role="tooltip"] b');
});

describe('grouped tenant menu items', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('tenant-menu-grouping');

        // A second tenant makes the switcher available, so the identity items (like `profile`) are rendered
        // before it rather than in the grouped lists.
        Team::factory()->count(2)->create();
        Filament::setTenant(Team::query()->first());
    });

    it('renders each registration group as a separate list, with `register` in the last group', function (): void {
        $groups = livewire(Topbar::class)->instance()->getTenantMenuItemGroupsAfterSwitcher();

        expect($groups)->toHaveCount(2)
            ->and($groups[0]->keys()->all())->toBe(['alpha', 'beta'])
            ->and($groups[1]->keys()->all())->toBe(['gamma', 'register']);
    });

    it('runs actions from different groups using `callAction()`', function (): void {
        livewire(Topbar::class)
            ->callAction('alpha')
            ->assertNotified('alpha ran')
            ->callAction('gamma')
            ->assertNotified('gamma ran');
    });

    it('keeps `profile` above the tenant switcher, out of the grouped lists', function (): void {
        $groupedNames = collect(livewire(Topbar::class)->instance()->getTenantMenuItemGroupsAfterSwitcher())
            ->flatMap(fn (Collection $group): array => $group->keys()->all())
            ->all();

        expect(array_keys(Filament::getCurrentPanel()->getTenantMenuItems()))->toContain('profile')
            ->and($groupedNames)->not->toContain('profile');
    });
});

describe('explicit `register` placement', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('tenant-menu-register-placement');

        Team::factory()->count(2)->create();
        Filament::setTenant(Team::query()->first());
    });

    it('keeps a registered `register` in its group instead of appending it to the last group', function (): void {
        $groups = livewire(Topbar::class)->instance()->getTenantMenuItemGroupsAfterSwitcher();

        expect($groups[0]->keys()->all())->toBe(['settings', 'register'])
            ->and($groups[array_key_last($groups)]->has('register'))->toBeFalse();
    });
});

describe('flat tenant menu items', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('tenant-menu-flat');

        Team::factory()->count(2)->create();
        Filament::setTenant(Team::query()->first());
    });

    it('renders a single list, merging items from successive `tenantMenuItems()` calls', function (): void {
        expect(Filament::getCurrentPanel()->hasMultipleTenantMenuItemGroups())->toBeFalse()
            ->and(array_keys(Filament::getCurrentPanel()->getTenantMenuItems()))->toContain('first', 'second');
    });

    it('runs actions using `callAction()`', function (): void {
        livewire(Topbar::class)
            ->callAction('first')
            ->assertNotified('first ran')
            ->callAction('second')
            ->assertNotified('second ran');
    });

    it('keeps searchable tenant popup controls in the tab order without menu semantics', function (): void {
        Filament::getCurrentPanel()->searchableTenantMenu();

        $document = new DOMDocument;
        @$document->loadHTML(livewire(Sidebar::class)->html());

        $xpath = new DOMXPath($document);
        $tenantMenu = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' fi-tenant-menu ')]")->item(0);

        expect($tenantMenu)->not->toBeNull()
            ->and($xpath->query('.//*[@role="menuitem"]', $tenantMenu))->toHaveCount(0)
            ->and($xpath->query('.//*[@role="button" and @tabindex="0"]', $tenantMenu))->toHaveCount(2)
            ->and($xpath->query('.//a[contains(concat(" ", normalize-space(@class), " "), " fi-dropdown-list-item ") and not(@tabindex)]', $tenantMenu))->toHaveCount(1);
    });
});

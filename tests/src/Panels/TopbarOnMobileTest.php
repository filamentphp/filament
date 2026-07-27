<?php

use Filament\Enums\DatabaseNotificationsPosition;
use Filament\Facades\Filament;
use Filament\Livewire\DatabaseNotifications;
use Filament\Livewire\GlobalSearch;
use Filament\Livewire\Sidebar;
use Filament\Livewire\Topbar;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(TestCase::class);

beforeEach(function (): void {
    actingAs(User::factory()->create());
});

describe('`topbarOnMobile()`', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('topbar-on-mobile');
    });

    it('adds the `fi-body-has-topbar-on-mobile` class to the body, without `fi-body-has-topbar`', function (): void {
        get(Filament::getUrl())
            ->assertSee('fi-body-has-topbar-on-mobile', escape: false)
            ->assertDontSee('fi-body-has-topbar ', escape: false)
            ->assertDontSee('fi-body-has-topbar"', escape: false);
    });

    it('renders the global search field, database notifications and user menu in the topbar', function (): void {
        livewire(Topbar::class)
            ->assertSee('fi-topbar-end', escape: false)
            ->assertSeeLivewire(GlobalSearch::class)
            ->assertSeeLivewire(DatabaseNotifications::class)
            ->assertSee('fi-user-menu', escape: false);
    });

    it('does not render the desktop-only topbar chrome, which is hidden on mobile anyway', function (): void {
        livewire(Topbar::class)
            ->assertDontSee('fi-topbar-start', escape: false)
            ->assertDontSee('fi-topbar-nav-groups', escape: false);
    });

    it('still renders the global search field, database notifications and user menu in the sidebar', function (): void {
        livewire(Sidebar::class)
            ->assertSee('fi-sidebar-global-search-ctn', escape: false)
            ->assertSee('fi-sidebar-footer', escape: false)
            ->assertSeeLivewire(GlobalSearch::class)
            ->assertSeeLivewire(DatabaseNotifications::class);
    });

    it('gives the topbar database notifications a distinct modal ID, so that opening one does not open both', function (): void {
        $topbarModalId = livewire(DatabaseNotifications::class, [
            'position' => DatabaseNotificationsPosition::Topbar,
            'modalId' => 'database-notifications-topbar',
        ])->instance()->getModalId();

        $sidebarModalId = livewire(DatabaseNotifications::class)->instance()->getModalId();

        expect($topbarModalId)->toBe('database-notifications-topbar')
            ->and($sidebarModalId)->toBe('database-notifications')
            ->and($topbarModalId)->not->toBe($sidebarModalId);
    });
});

it('does not render a topbar when only `topbar(false)` is used', function (): void {
    Filament::setCurrentPanel('no-topbar');

    get(Filament::getUrl())
        ->assertDontSee('fi-body-has-topbar-on-mobile', escape: false)
        ->assertDontSee('fi-topbar-ctn', escape: false);
});

it('does not add the `fi-body-has-topbar-on-mobile` class when the topbar is enabled', function (): void {
    Filament::setCurrentPanel('admin');

    get(Filament::getUrl())
        ->assertSee('fi-body-has-topbar', escape: false)
        ->assertDontSee('fi-body-has-topbar-on-mobile', escape: false);
});

it('renders the topbar on mobile and the sidebar items on desktop in the browser', function (): void {
    retry(10, function (): void {
        Filament::setCurrentPanel('topbar-on-mobile');

        visit(Filament::getUrl())
            ->on()->mobile()
            ->assertVisible('.fi-topbar-ctn .fi-global-search-ctn')
            ->assertVisible('.fi-topbar-ctn .fi-user-menu')
            ->assertDontSee('.fi-sidebar-footer')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit(Filament::getUrl())
            ->on()->desktop()
            ->assertDontSee('.fi-topbar-ctn')
            ->assertVisible('.fi-sidebar-footer .fi-user-menu')
            ->assertVisible('.fi-sidebar-global-search-ctn')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit(Filament::getUrl())
            ->on()->mobile()
            ->inDarkMode()
            ->assertNoAccessibilityIssues();

        visit(Filament::getUrl())
            ->on()->desktop()
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

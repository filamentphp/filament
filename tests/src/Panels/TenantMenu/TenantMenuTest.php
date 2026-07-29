<?php

use Filament\Facades\Filament;
use Filament\Livewire\Topbar;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    actingAs(User::factory()->create());

    // Grant access so the injected `profile` and `register` items resolve as visible.
    Gate::before(fn (): bool => true);
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
});

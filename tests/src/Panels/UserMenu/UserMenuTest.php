<?php

use Filament\Facades\Filament;
use Filament\Livewire\Topbar;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    actingAs(User::factory()->create());
});

describe('grouped user menu items', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('user-menu-grouping');
    });

    it('renders each registration group as a separate list, with `logout` in the last group', function (): void {
        $groups = Filament::getCurrentPanel()->getUserMenuItemGroupsAfterTheme();

        expect($groups)->toHaveCount(2)
            ->and($groups[0]->keys()->all())->toBe(['alpha', 'beta'])
            ->and($groups[1]->keys()->all())->toBe(['gamma', 'logout']);
    });

    it('runs actions from different groups using `callAction()`', function (): void {
        livewire(Topbar::class)
            ->callAction('alpha')
            ->assertNotified('alpha ran')
            ->callAction('gamma')
            ->assertNotified('gamma ran');
    });
});

describe('flat user menu items', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('user-menu-flat');
    });

    it('renders a single list, merging items from successive `userMenuItems()` calls', function (): void {
        expect(Filament::getCurrentPanel()->hasMultipleUserMenuItemGroups())->toBeFalse()
            ->and(array_keys(Filament::getCurrentPanel()->getUserMenuItems()))->toContain('first', 'second');
    });

    it('runs actions using `callAction()`', function (): void {
        livewire(Topbar::class)
            ->callAction('first')
            ->assertNotified('first ran')
            ->callAction('second')
            ->assertNotified('second ran');
    });
});

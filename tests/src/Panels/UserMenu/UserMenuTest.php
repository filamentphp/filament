<?php

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

describe('grouped user menu items', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('user-menu-grouping-fixture');
    });

    it('detects multiple registration groups', function (): void {
        expect(Filament::getCurrentPanel()->hasMultipleUserMenuItemGroups())->toBeTrue();
    });

    it('splits after-theme actions into one collection per group and appends logout to the last group', function (): void {
        $groups = Filament::getCurrentPanel()->getUserMenuItemGroupsAfterTheme();

        expect($groups)->toHaveCount(2)
            ->and($groups[0]->map(fn (Action $action): string => $action->getName())->values()->all())->toBe(['alpha', 'beta'])
            ->and($groups[1]->map(fn (Action $action): string => $action->getName())->values()->all())->toBe(['gamma', 'logout']);
    });

    it('reuses the same resolved action instances when getUserMenuItems is called more than once', function (): void {
        $panel = Filament::getCurrentPanel();

        $first = $panel->getUserMenuItems();
        $second = $panel->getUserMenuItems();

        expect($first['logout'])->toBe($second['logout'])
            ->and($first['alpha'])->toBe($second['alpha']);
    });

    it('uses the same logout instance in grouped collections as in the flat menu items', function (): void {
        $panel = Filament::getCurrentPanel();

        $flat = $panel->getUserMenuItems();
        $groups = $panel->getUserMenuItemGroupsAfterTheme();
        $lastGroup = $groups[array_key_last($groups)];

        expect($lastGroup->get('logout'))->toBe($flat['logout']);
    });
});

describe('sequential flat userMenuItems calls', function (): void {
    beforeEach(function (): void {
        Filament::setCurrentPanel('user-menu-flat-merge-fixture');
    });

    it('does not register multiple visual groups', function (): void {
        expect(Filament::getCurrentPanel()->hasMultipleUserMenuItemGroups())->toBeFalse();
    });

    it('merges items from successive flat registrations', function (): void {
        $names = array_keys(Filament::getCurrentPanel()->getUserMenuItems());

        expect($names)->toContain('first')->toContain('second');
    });
});

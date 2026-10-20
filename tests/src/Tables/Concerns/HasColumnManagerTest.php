<?php

use Filament\Tests\Fixtures\Livewire\ReorderableColumnsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('column preferences', function (): void {
    it('retains applied column and group preferences across reloads while reconciling new columns and `resetTableColumnManager()`', function (): void {
        $component = livewire(ReorderableColumnsTable::class);
        $columns = $component->get('tableColumns');
        $columns[0]['isToggled'] = false;
        $columns[1]['columns'] = [$columns[1]['columns'][2], $columns[1]['columns'][0]];
        $columns = [$columns[1], $columns[2], $columns[0]];

        $component->call('applyTableColumnManager', $columns, true);

        $reloaded = livewire(ReorderableColumnsTable::class, ['introduced' => true]);
        $state = $reloaded->get('tableColumns');

        expect(array_column($state, 'name'))->toBe(['Publication', 'id', 'title', 'author.name'])
            ->and(array_column($state[0]['columns'], 'name'))->toBe(['is_published', 'rating', 'internal', 'created_at'])
            ->and($state[0]['columns'][0]['isToggled'])->toBeFalse()
            ->and($state[0]['columns'][2]['isHidden'])->toBeTrue()
            ->and($state[0]['columns'][3]['isToggled'])->toBeTrue()
            ->and($state[2]['isToggled'])->toBeFalse()
            ->and(array_keys($reloaded->instance()->getTable()->getColumns()))->toBe(['is_published', 'rating', 'internal', 'created_at', 'id', 'title', 'author.name']);

        $reloaded->call('resetTableColumnManager');

        expect(array_column($reloaded->get('tableColumns'), 'name'))->toBe(['author.name', 'title', 'Publication', 'id']);

        livewire(ReorderableColumnsTable::class, ['introduced' => true])
            ->assertSet('tableColumns', $reloaded->get('tableColumns'));
    });

    it('does not restore an applied order when `persistColumnsInSession(false)` is configured', function (): void {
        $component = livewire(ReorderableColumnsTable::class, ['persist' => false]);
        $columns = $component->get('tableColumns');
        $component->call('applyTableColumnManager', [$columns[2], $columns[0], $columns[1]], true);

        expect(array_keys($component->instance()->getTable()->getColumns()))->toBe(['id', 'title', 'rating', 'internal', 'is_published']);

        livewire(ReorderableColumnsTable::class, ['persist' => false])->assertSet('tableColumns', $columns);
    });

});

describe('column manager reordering', function (): void {
    it('reorders columns and groups through hidden controls without changing deferred application or close semantics', function (string $layout, bool $deferred, bool $toggleable = true): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        Post::factory()->create();

        $page = visit('/column-manager-reordering-browser-test?' . http_build_query(['layout' => $layout, 'deferred' => $deferred, 'toggleable' => $toggleable]));
        $trigger = '#reorderable-columns button[aria-label="Column manager"]';
        $titleDown = '[data-testid="column-manager-title-move-down"]';
        $titleUp = '[data-testid="column-manager-title-move-up"]';
        $publishedUp = '[data-testid="column-manager-is_published-move-up"]';
        $publishedDown = '[data-testid="column-manager-is_published-move-down"]';
        $groupDown = '[data-testid="column-manager-Publication-move-down"]';
        $groupUp = '[data-testid="column-manager-Publication-move-up"]';
        $order = "Array.from(document.querySelectorAll('#reorderable-columns [data-testid$=\"-move-up\"]')).filter(control => ! control.closest('.fi-ta-col-manager-group-items')).map(control => (control.closest('.fi-ta-col-manager-group') ? 'group::' : 'column::') + control.dataset.testid.slice('column-manager-'.length, -'-move-up'.length))";
        $groupOrder = "Array.from(document.querySelectorAll('#reorderable-columns .fi-ta-col-manager-group-items [data-testid$=\"-move-up\"]')).map(control => 'column::' + control.dataset.testid.slice('column-manager-'.length, -'-move-up'.length))";

        $page->click($trigger);

        if ($layout === 'modal') {
            $page->assertPresent('[role="dialog"] :focus');
        }

        if (! $toggleable) {
            $page->assertMissing('#reorderable-columns .fi-ta-col-manager-items input[type="checkbox"]');
        }

        $page->assertScript("new Set(Array.from(document.querySelectorAll('#reorderable-columns [data-testid][aria-describedby]')).map(control => control.getAttribute('aria-describedby'))).size", 5)
            ->assertScript("Array.from(document.querySelectorAll('#reorderable-columns [data-testid][aria-describedby]')).every(control => document.getElementById(control.getAttribute('aria-describedby')) !== null)", true);

        $page->assertDisabled($titleUp)
            ->assertMissing('[data-testid="column-manager-internal-move-up"]')
            ->assertVisible($titleDown)
            ->keys($titleDown, 'Enter')
            ->assertScript($order, ['group::Publication', 'column::title', 'column::id'])
            ->assertPresent($titleDown . ':focus')
            ->assertPresent($titleDown . ':not([aria-disabled="true"])')
            ->keys($publishedUp, 'Space')
            ->assertScript($groupOrder, ['column::is_published', 'column::rating'])
            ->assertDisabled($publishedUp)
            ->assertPresent($publishedDown . ':focus')
            ->assertPresent($publishedDown . ':not([aria-disabled="true"])');

        $childDescription = '[id="' . $page->attribute($publishedDown, 'aria-describedby') . '"]';
        $page->assertSeeIn($publishedDown, __('filament-tables::table.column_manager.actions.move_down.label', ['column' => 'Published']))
            ->assertSeeIn($childDescription, __('filament-tables::table.column_manager.position_in_group', ['position' => 1, 'count' => 2, 'group' => 'Publication']))
            ->assertSeeIn('[role="status"]', __('filament-tables::table.column_manager.' . ($deferred ? 'reordered_in_group_deferred' : 'reordered_in_group'), ['column' => 'Published', 'position' => 1, 'count' => 2, 'group' => 'Publication']))
            ->keys($groupDown, 'Enter')
            ->assertPresent($groupDown . ':not([aria-disabled="true"])')
            ->keys($groupDown, 'Enter')
            ->assertScript($order, ['column::title', 'column::id', 'group::Publication'])
            ->assertPresent($groupUp . ':focus')
            ->assertDisabled($groupDown);

        $description = '[id="' . $page->attribute($groupUp, 'aria-describedby') . '"]';
        $page->assertSeeIn($description, __('filament-tables::table.column_manager.position', ['position' => 3, 'count' => 3]))
            ->assertSeeIn('[role="status"]', __('filament-tables::table.column_manager.' . ($deferred ? 'reordered_deferred' : 'reordered'), ['column' => 'Publication', 'position' => 3, 'count' => 3]));

        if ($deferred) {
            $page->assertNoAccessibilityIssues();
            $page->inDarkMode()->click($trigger)->assertNoAccessibilityIssues();

            $page->assertScript("Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating')) < Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-id'))", true);
        }

        $page->keys($groupUp, 'Escape')->click($trigger)
            ->assertScript($order, ['column::title', 'column::id', 'group::Publication']);

        if ($deferred) {
            $page->click('#reorderable-columns button:has-text("Apply columns")');
        }

        $page->assertScript("Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-id')) < Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating'))", true)
            ->assertNoSmoke();

        $page->refresh()->click($trigger)
            ->assertScript($order, ['column::title', 'column::id', 'group::Publication'])
            ->assertScript($groupOrder, ['column::is_published', 'column::rating'])
            ->click('#reorderable-columns button:has-text("Reset")')
            ->assertScript($order, ['column::title', 'group::Publication', 'column::id'])
            ->assertScript($groupOrder, ['column::rating', 'column::is_published']);
    })->with([
        'deferred dropdown' => ['dropdown', true],
        'live dropdown' => ['dropdown', false],
        'deferred modal' => ['modal', true],
        'no toggleable columns' => ['dropdown', true, false],
    ]);

    it('preserves draft and applied order when mixing drag handles and hidden move controls', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        Post::factory()->create();

        $page = visit('/column-manager-reordering-browser-test');
        $order = "Array.from(document.querySelectorAll('#reorderable-columns [data-testid$=\"-move-up\"]')).filter(control => ! control.closest('.fi-ta-col-manager-group-items')).map(control => (control.closest('.fi-ta-col-manager-group') ? 'group::' : 'column::') + control.dataset.testid.slice('column-manager-'.length, -'-move-up'.length))";
        $groupOrder = "Array.from(document.querySelectorAll('#reorderable-columns .fi-ta-col-manager-group-items [data-testid$=\"-move-up\"]')).map(control => 'column::' + control.dataset.testid.slice('column-manager-'.length, -'-move-up'.length))";

        $page->click('#reorderable-columns button[aria-label="Column manager"]')
            ->drag('.fi-ta-col-manager-item:has([data-testid="column-manager-id-move-up"]) .fi-ta-col-manager-reorder-handle', '.fi-ta-col-manager-item:has([data-testid="column-manager-title-move-up"]) .fi-ta-col-manager-reorder-handle')
            ->assertScript($order, ['column::id', 'column::title', 'group::Publication'])
            ->keys('[data-testid="column-manager-title-move-down"]', 'Enter')
            ->assertScript($order, ['column::id', 'group::Publication', 'column::title'])
            ->drag('.fi-ta-col-manager-item:has([data-testid="column-manager-title-move-up"]) .fi-ta-col-manager-reorder-handle', '.fi-ta-col-manager-item:has([data-testid="column-manager-id-move-up"]) .fi-ta-col-manager-reorder-handle')
            ->assertScript($order, ['column::title', 'column::id', 'group::Publication'])
            ->drag('.fi-ta-col-manager-item:has([data-testid="column-manager-is_published-move-up"]) .fi-ta-col-manager-reorder-handle', '.fi-ta-col-manager-item:has([data-testid="column-manager-rating-move-up"]) .fi-ta-col-manager-reorder-handle')
            ->assertScript($groupOrder, ['column::is_published', 'column::rating'])
            ->keys('[data-testid="column-manager-is_published-move-down"]', 'Space')
            ->assertScript($groupOrder, ['column::rating', 'column::is_published'])
            ->drag('.fi-ta-col-manager-item:has([data-testid="column-manager-is_published-move-up"]) .fi-ta-col-manager-reorder-handle', '.fi-ta-col-manager-item:has([data-testid="column-manager-rating-move-up"]) .fi-ta-col-manager-reorder-handle')
            ->assertScript($groupOrder, ['column::is_published', 'column::rating'])
            ->assertScript("Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating')) < Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-id'))", true);
        $page->click('#reorderable-columns button:has-text("Apply columns")')
            ->assertScript("Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-id')) < Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating'))", true)
            ->refresh()->click('#reorderable-columns button[aria-label="Column manager"]')
            ->assertScript($order, ['column::title', 'column::id', 'group::Publication'])
            ->assertScript($groupOrder, ['column::is_published', 'column::rating'])
            ->assertNoSmoke();
    });

    it('keeps move controls available without adding them or drag handles to the Tab order', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        Post::factory()->create();

        $page = visit('/column-manager-reordering-browser-test');
        $page->click('#reorderable-columns button[aria-label="Column manager"]')
            ->assertAttribute('[data-testid="column-manager-title-move-down"]', 'tabindex', '-1')
            ->assertScript("Array.from(document.querySelectorAll('#reorderable-columns .fi-ta-col-manager-reorder-handle')).every(handle => handle.tabIndex === -1 && handle.getAttribute('aria-hidden') === 'true')", true)
            ->keys('#reorderable-columns input[id$="-title"]', 'Tab')
            ->assertNotPresent('[data-testid^="column-manager-"]:focus')
            ->assertNoSmoke();
    });
});

describe('closing the column manager dropdown on apply', function (): void {
    it('closes the dropdown once deferred columns are applied with `closeColumnManagerDropdownOnApply()`', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        Post::factory()->create();

        $trigger = '#reorderable-columns button[aria-label="Column manager"]';
        $titleDown = '[data-testid="column-manager-title-move-down"]';
        $titleIsAfterRating = "Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-title')) > Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating'))";

        visit('/column-manager-reordering-browser-test?closeOnApply=1')
            ->click($trigger)
            ->assertVisible($titleDown)
            ->keys($titleDown, 'Enter')
            ->assertEnabled('[data-testid="column-manager-title-move-up"]')
            ->assertScript($titleIsAfterRating, false)
            ->click('#reorderable-columns button:has-text("Apply columns")')
            ->assertMissing($titleDown)
            ->assertPresent($trigger . ':focus')
            ->assertScript($titleIsAfterRating, true)
            ->assertNoSmoke();
    });

    it('keeps the dropdown open after applying deferred columns by default', function (): void {
        Artisan::call('filament:assets');
        $this->actingAs(User::factory()->create());
        Post::factory()->create();

        $titleDown = '[data-testid="column-manager-title-move-down"]';

        visit('/column-manager-reordering-browser-test')
            ->click('#reorderable-columns button[aria-label="Column manager"]')
            ->assertVisible($titleDown)
            ->keys($titleDown, 'Enter')
            ->assertEnabled('[data-testid="column-manager-title-move-up"]')
            ->click('#reorderable-columns button:has-text("Apply columns")')
            ->assertScript("Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-title')) > Array.from(document.querySelectorAll('#reorderable-columns th')).findIndex(header => header.classList.contains('fi-ta-header-cell-rating'))", true)
            ->assertVisible($titleDown)
            ->assertNoSmoke();
    });
});

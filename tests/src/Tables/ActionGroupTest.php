<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('scopes row and bulk `ActionGroup` menu attributes and keyboard interactions', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $firstPost = Post::factory()->create(['title' => 'First record']);
    $secondPost = Post::factory()->create(['title' => 'Second record']);

    $firstGroup = "[data-testid=\"inspect-{$firstPost->id}\"]";
    $firstTrigger = "{$firstGroup} > .fi-dropdown-trigger button";
    $firstItem = "{$firstGroup} [data-testid=\"inspect-item\"]";
    $disabledItem = "{$firstGroup} [data-testid=\"disabled-item\"]";
    $adjacentGroup = "[data-testid=\"more-{$firstPost->id}\"]";
    $adjacentTrigger = "{$adjacentGroup} > .fi-dropdown-trigger button";
    $linkItem = "{$adjacentGroup} [data-testid=\"link-item\"]";
    $secondGroup = "[data-testid=\"inspect-{$secondPost->id}\"]";
    $secondTrigger = "{$secondGroup} > .fi-dropdown-trigger button";
    $bulkTrigger = '[data-testid="bulk-group"] > .fi-dropdown-trigger [aria-haspopup="menu"]';
    $bulkItem = '[data-testid="count-item"]';
    $disabledBulkItem = '[data-testid="disabled-bulk-item"]';

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/table-action-groups-browser-test');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page
            ->assertScript('(() => { const triggers = Array.from(document.querySelectorAll(\'tbody [aria-haspopup="menu"]\')); return triggers.length === 4 && new Set(triggers.map(trigger => trigger.id)).size === 4 && new Set(triggers.map(trigger => trigger.getAttribute("aria-controls"))).size === 4 && triggers.every(trigger => { const panel = document.getElementById(trigger.getAttribute("aria-controls")); return panel.getAttribute("role") === "menu" && panel.getAttribute("aria-labelledby") === trigger.id && trigger.getAttribute("aria-expanded") === "false"; }); })()', true)
            ->keys($firstTrigger, 'Enter')
            ->assertAttribute($firstTrigger, 'aria-expanded', 'true')
            ->assertAttribute($adjacentTrigger, 'aria-expanded', 'false')
            ->assertAttribute($secondTrigger, 'aria-expanded', 'false')
            ->assertScript("document.activeElement.matches('{$firstItem}')", true)
            ->assertAttribute($firstItem, 'role', 'menuitem')
            ->assertAttribute($firstItem, 'tabindex', '-1')
            ->keys($firstItem, 'ArrowDown')
            ->assertScript("document.activeElement.matches('{$disabledItem}')", true)
            ->assertAttribute($disabledItem, 'aria-disabled', 'true')
            ->keys($disabledItem, 'Enter')
            ->assertSeeIn('[data-testid="last-action"]', 'None')
            ->keys($disabledItem, 'ArrowDown')
            ->assertScript("document.activeElement.matches('{$firstItem}')", true)
            ->assertScript('document.getAnimations().length', 0)
            ->assertNoAccessibilityIssues()
            ->keys($firstItem, 'Tab')
            ->assertAttribute($firstTrigger, 'aria-expanded', 'false')
            ->assertScript("document.activeElement.matches('{$adjacentTrigger}')", true)
            ->keys($adjacentTrigger, 'ArrowUp')
            ->assertScript("document.activeElement.matches('{$linkItem}')", true)
            ->assertAttribute($linkItem, 'role', 'menuitem')
            ->keys($linkItem, 'Escape')
            ->assertScript("document.activeElement.matches('{$adjacentTrigger}')", true)
            ->keys($secondTrigger, 'Enter')
            ->assertScript("document.activeElement.matches('{$secondGroup} [data-testid=\"inspect-item\"]')", true)
            ->keys("{$secondGroup} [data-testid=\"inspect-item\"]", 'Enter')
            ->assertSeeIn('[data-testid="last-action"]', 'Second record')
            ->assertScript("document.activeElement.matches('{$secondTrigger}')", true)
            ->keys($firstTrigger, 'Enter')
            ->assertScript("document.activeElement.matches('{$firstItem}')", true)
            ->keys($firstItem, 'Escape')
            ->click('thead input[type="checkbox"]')
            ->assertVisible($bulkTrigger)
            ->keys($bulkTrigger, 'ArrowUp')
            ->assertScript("document.activeElement.matches('{$disabledBulkItem}')", true)
            ->assertAttribute($disabledBulkItem, 'aria-disabled', 'true')
            ->keys($disabledBulkItem, 'Enter')
            ->assertSeeIn('[data-testid="last-action"]', 'Second record')
            ->assertScript('(() => { const trigger = document.querySelector(\'[data-testid="bulk-group"] [aria-haspopup="menu"]\'); const panel = document.getElementById(trigger.getAttribute("aria-controls")); return panel.getAttribute("role") === "menu" && panel.getAttribute("aria-labelledby") === trigger.id && trigger.getAttribute("aria-expanded") === "true"; })()', true)
            ->assertScript('document.getAnimations().length', 0)
            ->assertNoAccessibilityIssues()
            ->keys($disabledBulkItem, 'ArrowDown')
            ->assertScript("document.activeElement.matches('{$bulkItem}')", true)
            ->keys($bulkItem, 'Enter')
            ->assertSeeIn('[data-testid="last-action"]', 'Selected 2')
            ->assertScript("document.activeElement.matches('{$bulkTrigger}')", true)
            ->assertAttribute($bulkTrigger, 'aria-expanded', 'false')
            ->keys($bulkTrigger, 'Enter')
            ->assertScript("document.activeElement.matches('{$bulkItem}')", true)
            ->assertAttribute($bulkItem, 'role', 'menuitem')
            ->keys($bulkItem, 'Escape')
            ->assertScript("document.activeElement.matches('{$bulkTrigger}')", true)
            ->click('thead input[type="checkbox"]')
            ->assertMissing($bulkTrigger)
            ->assertNoSmoke();
    }
});

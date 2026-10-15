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
    $firstTrigger = "{$firstGroup} [aria-haspopup=\"menu\"]";
    $firstItem = "{$firstGroup} [data-testid=\"inspect-item\"]";
    $disabledItem = "{$firstGroup} [data-testid=\"disabled-item\"]";
    $adjacentGroup = "[data-testid=\"more-{$firstPost->id}\"]";
    $adjacentTrigger = "{$adjacentGroup} [aria-haspopup=\"menu\"]";
    $linkItem = "{$adjacentGroup} [data-testid=\"link-item\"]";
    $secondGroup = "[data-testid=\"inspect-{$secondPost->id}\"]";
    $secondTrigger = "{$secondGroup} [aria-haspopup=\"menu\"]";
    $bulkTrigger = '[data-testid="bulk-group"] [aria-haspopup="menu"]';
    $bulkItem = '[data-testid="count-item"]';
    $disabledBulkItem = '[data-testid="disabled-bulk-item"]';

    $page = visit('/table-action-groups-browser-test');

    $page
        ->assertScript('(() => { const triggers = Array.from(document.querySelectorAll(\'tbody [aria-haspopup="menu"]\')); return triggers.length === 4 && new Set(triggers.map(trigger => trigger.id)).size === 4 && new Set(triggers.map(trigger => trigger.getAttribute("aria-controls"))).size === 4 && triggers.every(trigger => { const panel = document.getElementById(trigger.getAttribute("aria-controls")); return panel.getAttribute("role") === "menu" && panel.getAttribute("aria-labelledby") === trigger.id && trigger.getAttribute("aria-expanded") === "false"; }); })()', true)
        ->keys($firstTrigger, 'Enter')
        ->assertAttribute($firstTrigger, 'aria-expanded', 'true')
        ->assertAttribute($adjacentTrigger, 'aria-expanded', 'false')
        ->assertAttribute($secondTrigger, 'aria-expanded', 'false')
        ->assertPresent($firstItem . ':focus')
        ->assertAttribute($firstItem, 'role', 'menuitem')
        ->assertAttribute($firstItem, 'tabindex', '-1')
        ->keys($firstItem, 'ArrowDown')
        ->assertPresent($disabledItem . ':focus')
        ->assertAttribute($disabledItem, 'aria-disabled', 'true')
        ->keys($disabledItem, 'Enter')
        ->assertSeeIn('[data-testid="last-action"]', 'None')
        ->keys($disabledItem, 'ArrowDown')
        ->assertPresent($firstItem . ':focus')
        ->assertNoAccessibilityIssues()
        ->keys($firstItem, 'Tab')
        ->assertAttribute($firstTrigger, 'aria-expanded', 'false')
        ->assertPresent($adjacentTrigger . ':focus')
        ->keys($adjacentTrigger, 'ArrowUp')
        ->assertPresent($linkItem . ':focus')
        ->assertAttribute($linkItem, 'role', 'menuitem')
        ->keys($linkItem, 'Escape')
        ->assertPresent($adjacentTrigger . ':focus')
        ->keys($secondTrigger, 'Enter')
        ->assertPresent("{$secondGroup} [data-testid=\"inspect-item\"]:focus")
        ->keys("{$secondGroup} [data-testid=\"inspect-item\"]", 'Enter')
        ->assertSeeIn('[data-testid="last-action"]', 'Second record')
        ->assertPresent($secondTrigger . ':focus')
        ->keys($firstTrigger, 'Enter')
        ->assertPresent($firstItem . ':focus')
        ->keys($firstItem, 'Escape')
        ->click('thead input[type="checkbox"]')
        ->assertVisible($bulkTrigger)
        ->keys($bulkTrigger, 'ArrowUp')
        ->assertPresent($disabledBulkItem . ':focus')
        ->assertAttribute($disabledBulkItem, 'aria-disabled', 'true')
        ->keys($disabledBulkItem, 'Enter')
        ->assertSeeIn('[data-testid="last-action"]', 'Second record')
        ->assertScript('(() => { const trigger = document.querySelector(\'[data-testid="bulk-group"] [aria-haspopup="menu"]\'); const panel = document.getElementById(trigger.getAttribute("aria-controls")); return panel.getAttribute("role") === "menu" && panel.getAttribute("aria-labelledby") === trigger.id && trigger.getAttribute("aria-expanded") === "true"; })()', true)
        ->assertNoAccessibilityIssues()
        ->keys($disabledBulkItem, 'ArrowDown')
        ->assertPresent($bulkItem . ':focus')
        ->keys($bulkItem, 'Enter')
        ->assertSeeIn('[data-testid="last-action"]', 'Selected 2')
        ->assertPresent($bulkTrigger . ':focus')
        ->assertAttribute($bulkTrigger, 'aria-expanded', 'false')
        ->keys($bulkTrigger, 'Enter')
        ->assertPresent($bulkItem . ':focus')
        ->assertAttribute($bulkItem, 'role', 'menuitem')
        ->keys($bulkItem, 'Escape')
        ->assertPresent($bulkTrigger . ':focus')
        ->click('thead input[type="checkbox"]')
        ->assertMissing($bulkTrigger)
        ->assertNoSmoke();

    $page->inDarkMode()
        ->keys($firstTrigger, 'Enter')
        ->assertPresent($firstItem . ':focus')
        ->assertNoAccessibilityIssues()
        ->keys($firstItem, 'Escape')
        ->click('thead input[type="checkbox"]')
        ->assertVisible($bulkTrigger)
        ->keys($bulkTrigger, 'Enter')
        ->assertAttribute($bulkTrigger, 'aria-expanded', 'true')
        ->assertPresent($bulkItem . ':focus')
        ->assertNoAccessibilityIssues();
});

<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('selects only query-eligible records across pages and preserves explicit deselections', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $posts = Post::factory()->count(3)->create(['is_published' => true]);
    $unpublishedPost = Post::factory()->create(['is_published' => false]);
    $firstCheckbox = "tbody input[type=\"checkbox\"][value=\"{$posts[0]->id}\"]";

    $page = visit('/table-query-selection-browser-test');

    $page
        ->click('thead input[type="checkbox"]')
        ->click('Select all 3')
        ->click('[data-testid="inspect-selection"]')
        ->assertScript('document.querySelector(\'[data-testid="selected-keys"]\').textContent.trim()', $posts->pluck('id')->implode(','))
        ->click($firstCheckbox)
        ->click('[data-testid="inspect-selection"]')
        ->assertScript('document.querySelector(\'[data-testid="selected-keys"]\').textContent.trim()', $posts->skip(1)->pluck('id')->implode(','))
        ->click('button[aria-label="Next"]:visible')
        ->assertSee($unpublishedPost->title)
        ->assertMissing("tbody input[type=\"checkbox\"][value=\"{$unpublishedPost->id}\"]")
        ->assertChecked("tbody input[type=\"checkbox\"][value=\"{$posts[2]->id}\"]")
        ->assertNoAccessibilityIssues()
        ->assertNoSmoke();

    $page->inDarkMode()->assertNoAccessibilityIssues();
});

it('keeps the deselect-all button enabled while the table polls', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $post = Post::factory()->create(['is_published' => true]);

    $page = visit('/table-query-selection-browser-test?pollSelection=1');

    $page
        ->click("tbody input[type=\"checkbox\"][value=\"{$post->id}\"]")
        ->assertVisible('button:has-text("Deselect all")');

    $stayedEnabled = $page->script(<<<'JS'
        new Promise((resolve) => {
            const button = [...document.querySelectorAll('button')].find((element) => element.textContent.trim() === 'Deselect all');
            const pollSelector = '[data-testid="selection-poll"]';
            const initialCount = Number(document.querySelector(pollSelector).textContent);
            let wasDisabled = button.getAttribute('aria-disabled') === 'true';

            const observer = new MutationObserver((mutations) => {
                wasDisabled ||= button.getAttribute('aria-disabled') === 'true'
                    || mutations.some((mutation) => mutation.target === button && mutation.oldValue === 'true');

                if (Number(document.querySelector(pollSelector).textContent) > initialCount) {
                    observer.disconnect();
                    resolve(!wasDisabled);
                }
            });

            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['aria-disabled'],
                attributeOldValue: true,
                characterData: true,
                childList: true,
                subtree: true,
            });
        })
        JS);

    expect($stayedEnabled)->toBeTrue();
});

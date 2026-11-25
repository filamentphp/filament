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

it('selects and deselects only owned row inputs on the current page', function (bool $nested, bool $currentPageOnly): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
    $posts = Post::factory()->count(3)->create(['is_published' => true]);
    $nestedPost = Post::factory()->create(['is_published' => false]);
    $posts[0]->update(['title' => '<option class="fi-ta-record-checkbox">' . $posts[2]->id . '</option><span class="fi-ta-record-checkbox">Not a checkbox</span>']);
    $page = visit('/table-query-selection-browser-test?nested=' . (int) $nested . '&currentPageOnly=' . (int) $currentPageOnly);
    $selectPage = '[data-testid="selection-table"] thead input[type="checkbox"]:not([data-testid="nested-selection-table"] *)';
    $firstCheckbox = 'input[type="checkbox"][value="' . $posts[0]->id . '"]';
    $readSubmittedKeys = 'JSON.parse(document.querySelector(\'[data-testid="submitted-keys"]\').textContent).sort()';

    $page
        ->assertPresent('[data-testid="selection-table"] option')
        ->click($selectPage)
        ->click('[data-testid="inspect-selection"]')
        ->assertScript($readSubmittedKeys, array_map(strval(...), $posts->take(2)->modelKeys()))
        ->assertScript('document.querySelector(\'[data-testid="selected-keys"]\').textContent.trim()', $posts->take(2)->pluck('id')->implode(','))
        ->click($selectPage)
        ->assertNotChecked($firstCheckbox)
        ->assertMissing('[data-testid="inspect-selection"]')
        ->click($firstCheckbox)
        ->click('[data-testid="inspect-selection"]')
        ->assertScript($readSubmittedKeys, [(string) $posts[0]->id]);

    if ($nested) {
        $page->assertNotChecked('[data-testid="nested-selection-table"] input[value="' . $nestedPost->id . '"]');
    }

    $page
        ->click('Deselect all')
        ->assertNotChecked($firstCheckbox)
        ->click('button[aria-label="Next"]:visible')
        ->assertPresent('input[type="checkbox"][value="' . $posts[2]->id . '"]')
        ->click($selectPage)
        ->click('[data-testid="inspect-selection"]')
        ->assertScript($readSubmittedKeys, [(string) $posts[2]->id])
        ->assertNoSmoke();
})->with([false, true])->with([false, true]);

it('limits shift-click ranges and anchors to owned row inputs', function (bool $nested): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
    $posts = Post::factory()->count(4)->create(['is_published' => true]);
    $nestedPost = Post::factory()->create(['is_published' => false]);
    $posts[0]->update(['title' => '<option class="fi-ta-record-checkbox">' . $posts[3]->id . '</option>']);
    $page = visit('/table-query-selection-browser-test?pageSize=3&nested=' . (int) $nested);
    $firstCheckbox = 'input[type="checkbox"][value="' . $posts[0]->id . '"]';
    $lastCheckbox = 'input[type="checkbox"][value="' . $posts[2]->id . '"]';
    $readSubmittedKeys = 'JSON.parse(document.querySelector(\'[data-testid="submitted-keys"]\').textContent).sort()';

    $page->click($firstCheckbox);

    if ($nested) {
        $page->click('[data-testid="nested-selection-table"] input[value="' . $nestedPost->id . '"]');
    } else {
        $page->click('[data-testid="selection-table"] option');
    }

    $page
        ->withKeyDown('Shift', fn () => $page->click($lastCheckbox))
        ->click('[data-testid="inspect-selection"]')
        ->assertScript($readSubmittedKeys, array_map(strval(...), $posts->take(3)->modelKeys()))
        ->withKeyDown('Shift', fn () => $page->click($firstCheckbox))
        ->assertNotChecked($lastCheckbox)
        ->assertMissing('[data-testid="inspect-selection"]')
        ->assertNoSmoke();
})->with([false, true]);

it('preserves disabled row checkboxes at the selection limit', function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
    $posts = Post::factory()->count(3)->create(['is_published' => true]);
    $page = visit('/table-query-selection-browser-test?pageSize=3&selectionLimit=2');
    $firstCheckbox = 'input[type="checkbox"][value="' . $posts[0]->id . '"]';
    $secondCheckbox = 'input[type="checkbox"][value="' . $posts[1]->id . '"]';
    $thirdCheckbox = 'input[type="checkbox"][value="' . $posts[2]->id . '"]';

    $page
        ->click($firstCheckbox)
        ->click($secondCheckbox)
        ->assertDisabled($thirdCheckbox)
        ->assertDisabled('thead input[type="checkbox"]')
        ->click($firstCheckbox)
        ->assertEnabled($thirdCheckbox)
        ->click($thirdCheckbox)
        ->click('[data-testid="inspect-selection"]')
        ->assertScript('JSON.parse(document.querySelector(\'[data-testid="submitted-keys"]\').textContent).sort()', array_values(array_map(strval(...), $posts->skip(1)->modelKeys())))
        ->assertNoSmoke();
});

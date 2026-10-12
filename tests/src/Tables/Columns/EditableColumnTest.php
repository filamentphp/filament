<?php

namespace Filament\Tests\Tables\Columns;

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ColumnsBrowserTest;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('persists boolean changes through `updateTableColumnState()` and rejects validation and disabled updates', function (string $columnName): void {
    $post = Post::factory()->create(['is_published' => false, 'json' => ['approved' => false]]);
    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true]);

    foreach ([true, false] as $state) {
        $component->call('updateTableColumnState', $columnName, (string) $post->getKey(), $state)
            ->assertReturned($state);

        expect(data_get($post->fresh(), $columnName))->toBe($state);
    }

    $component->set('saveScenario', 'validation')
        ->call('updateTableColumnState', $columnName, (string) $post->getKey(), true);

    expect($component->effects['returns'][0])->toHaveKey('error')
        ->and(data_get($post->fresh(), $columnName))->toBeFalse();

    $component->set('saveScenario', 'disabled')
        ->call('updateTableColumnState', $columnName, (string) $post->getKey(), true)
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect(data_get($post->fresh(), $columnName))->toBeFalse();
})->with(['is_published', 'json.approved']);

it('describes validation errors at the focus owner and clears them on a successful correction in both themes', function (): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create();
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['is_published' => false, 'title' => 'Draft title', 'rating' => 1, 'content' => 'draft', 'json' => ['approved' => false, 'status' => 'draft']]);
        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=validation');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $controls = [
            'is_published' => 'input[type="checkbox"]',
            'json-approved' => '[role="switch"]',
            'title' => 'input:not([type="hidden"])',
            'rating' => 'select',
            'content' => '[role="combobox"]',
            'json-status' => 'button[aria-haspopup="listbox"]',
        ];

        foreach ($controls as $name => $controlSelector) {
            $column = '[data-testid="editable-' . $name . '"]';
            $control = $column . ' ' . $controlSelector;

            if ($name === 'title') {
                $page->type($control, 'Updated title')->keys($control, 'Tab');
            } elseif ($name === 'rating') {
                $page->select($control, '2');
            } elseif ($name === 'content') {
                $page->keys($control, ['Enter', 'ArrowDown', 'Enter']);
            } elseif ($name === 'json-status') {
                $page->click($control)->type($column . ' input[role="combobox"]', 'pub')
                    ->keys($column . ' input[role="combobox"]', ['ArrowDown', 'Enter']);
            } else {
                $page->click($control);
            }

            $page->assertAttribute($control, 'aria-invalid', 'true')
                ->assertVisible($column . ' [x-ref="error"]')
                ->assertScript('document.querySelector(' . json_encode($control) . ').getAttribute("aria-describedby").split(" ").includes(document.querySelector(' . json_encode($column . ' [x-ref="error"]') . ').id)');

            if (in_array($name, ['content', 'json-status'])) {
                $page->assertSeeIn($control, 'Draft');
            }

            if ($name !== 'json-approved') {
                $page->assertAttributeContains($control, 'aria-describedby', 'editable-column-help');
            }
        }

        expect($post->fresh())->is_published->toBeFalse()->title->toBe('Draft title')->rating->toBe(1)->content->toBe('draft');
        expect($post->fresh()->json)->approved->toBeFalse()->status->toBe('draft');

        $page->assertNoAccessibilityIssues();
        $page->script('Livewire.find(document.querySelector("[data-testid=editable-title]").closest("[wire\\\\:id]").getAttribute("wire:id")).set("saveScenario", "success")');
        $page->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-busy')
            ->type('[data-testid="editable-title"] input:not([type="hidden"])', 'Corrected title')
            ->keys('[data-testid="editable-title"] input:not([type="hidden"])', 'Tab')
            ->assertValue('[data-testid="editable-title"] input[x-ref="serverState"]', 'Corrected title')
            ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-invalid')
            ->assertScript('document.querySelector("[data-testid=editable-title] [x-ref=error]").checkVisibility()', false)
            ->assertAttributeContains('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-describedby', 'editable-column-help')
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();

        expect($post->fresh()->title)->toBe('Corrected title');
    }
});

it('displays persisted state after save hooks and `void` callbacks in both themes', function (string $scenario, string $expectedState, bool $isBatched = false): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create(['title' => 'Original title']);
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['title' => 'Original title']);
        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->assertScript('typeof Alpine.$data(document.querySelector("[data-testid=editable-title]")).blockEdit', 'function')
            ->type('[data-testid="editable-title"] input:not([type="hidden"])', 'mixedCase');

        if ($isBatched) {
            // Queue a renderless options request alongside the public save flow.
            $page->script('document.querySelector("[data-testid=editable-title] input:not([type=hidden])").blur(); Livewire.find(document.querySelector("[data-testid=editable-title]").closest("[wire\\\\:id]").getAttribute("wire:id")).callTableColumnMethod("content", "' . $post->getKey() . '", "getOptionsForJs")');
        } else {
            $page->keys('[data-testid="editable-title"] input:not([type="hidden"])', 'Tab');
        }

        $page->assertValue('[data-testid="editable-title"] input[x-ref="serverState"]', $expectedState)
            ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-busy');

        expect($post->fresh()->title)->toBe($expectedState);

        $page->assertValue('[data-testid="editable-title"] input:not([type="hidden"])', $expectedState)
            ->assertValue('[data-testid="editable-title"] input[x-ref="serverState"]', $expectedState)
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();
    }
})->with([
    'normalization' => ['normalize', 'MIXEDCASE'],
    'cached column state' => ['cached-state', 'mixedCase'],
    'custom records' => ['custom-records', 'MIXEDCASE'],
    'batched normalization' => ['normalize', 'MIXEDCASE', true],
]);

it('blocks rapid toggle and custom select edits without losing focus while saving in both themes', function (): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create();
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['content' => 'draft', 'json' => ['approved' => false]]);
        $page = visit('/columns-browser-test?editableColumns=1');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $toggle = '[data-testid="editable-json-approved"] [role="switch"]';
        $select = '[data-testid="editable-content"] [role="combobox"]';

        // Hold real save requests until the second interaction has been exercised.
        $page->script('window.editableFetch = window.fetch; window.fetch = (...parameters) => parameters[1]?.body?.includes("updateTableColumnState") ? new Promise(resolve => { window.resumeEditableSave = () => { window.resumeEditableSave = null; resolve(window.editableFetch(...parameters)) } }) : window.editableFetch(...parameters)');

        $page->keys($toggle, 'Space')
            ->assertAttribute($toggle, 'aria-busy', 'true')
            ->assertAttribute($toggle, 'aria-disabled', 'true')
            ->keys($toggle, 'Space')
            ->assertPresent($toggle . ':focus');

        $page->assertScript('typeof window.resumeEditableSave', 'function');
        $page->script('window.resumeEditableSave()');
        $page->assertValue('[data-testid="editable-json-approved"] input[x-ref="serverState"]', '1')
            ->assertAttributeMissing($toggle, 'aria-busy')
            ->assertAttribute($toggle, 'aria-checked', 'true')
            ->keys($select, ['Enter', 'ArrowDown', 'Enter'])
            ->assertAttribute($select, 'aria-busy', 'true')
            ->assertAttribute($select, 'aria-disabled', 'true')
            ->keys($select, 'Enter')
            ->assertAttribute($select, 'aria-expanded', 'false')
            ->assertPresent($select . ':focus');

        $page->assertScript('typeof window.resumeEditableSave', 'function');
        $page->script('window.resumeEditableSave()');
        $page->assertValue('[data-testid="editable-content"] input[x-ref="serverState"]', 'published')
            ->assertAttributeMissing($select, 'aria-busy')
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();

        expect($post->fresh()->json['approved'])->toBeTrue();
        expect($post->fresh()->content)->toBe('published');
    }
});

it('releases a failed save without claiming rollback and allows a later correction in both themes', function (string $scenario): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create();
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['title' => 'Original title']);
        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        // Suppress Livewire's global dialog only in this test to inspect the underlying control.
        $page->script('Livewire.hook("request", ({ fail }) => fail(({ preventDefault }) => preventDefault()))');
        $control = '[data-testid="editable-title"] input:not([type="hidden"])';

        $page->type($control, 'Attempted title')->keys($control, 'Tab')
            ->assertAttribute($control, 'aria-invalid', 'true')
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertEnabled($control)
            ->assertVisible('[data-testid="editable-title"] [x-ref="error"]')
            ->assertNoAccessibilityIssues();

        expect($post->fresh()->title)->toBe($scenario === 'post-save-failure' ? 'Attempted title' : 'Original title');

        $page->script('Livewire.find(document.querySelector("[data-testid=editable-title]").closest("[wire\\\\:id]").getAttribute("wire:id")).set("saveScenario", "success")');
        $page->type($control, 'Corrected title')->keys($control, 'Tab')
            ->assertValue('[data-testid="editable-title"] input[x-ref="serverState"]', 'Corrected title')
            ->assertAttributeMissing($control, 'aria-invalid')
            ->assertNoAccessibilityIssues();

        expect($post->fresh()->title)->toBe('Corrected title');
    }
})->with(['failure', 'forbidden', 'post-save-failure']);

it('releases a save whose response body is interrupted after headers in both themes', function (): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create(['title' => 'Original title']);
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['title' => 'Original title']);
        $page = visit('/columns-browser-test?editableColumns=1');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        // Persist through the real request, then interrupt only its response body.
        $page->script('window.editableFetch = window.fetch; window.addEventListener("unhandledrejection", event => event.preventDefault()); window.fetch = async (...parameters) => { const response = await window.editableFetch(...parameters); if (!parameters[1]?.body?.includes("updateTableColumnState")) return response; window.editableBodyInterrupted = true; return new Response(new ReadableStream({ start(controller) { controller.error(new TypeError("Response body interrupted")) } }), { status: response.status, headers: response.headers }) }; window.editableBodyInterrupted = false');

        $control = '[data-testid="editable-title"] input:not([type="hidden"])';
        $page->type($control, 'Updated title')->keys($control, 'Tab')
            ->assertScript('window.editableBodyInterrupted');

        expect($post->fresh()->title)->toBe('Updated title');

        $page->assertAttribute($control, 'aria-invalid', 'true')
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertEnabled($control)
            ->assertValue($control, 'Updated title')
            ->assertVisible('[data-testid="editable-title"] [x-ref="error"]')
            ->assertNoAccessibilityIssues();
    }
});

it('reflects a late `disabled()` refusal and restored permission on custom selects in both themes', function (string $columnName): void {
    Artisan::call('filament:assets');
    $post = Post::factory()->create();
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $post->refresh()->update(['content' => 'draft', 'json' => ['status' => 'draft']]);
        $page = visit('/columns-browser-test?editableColumns=1');

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $column = '[data-testid="editable-' . str_replace('.', '-', $columnName) . '"]';
        $control = $column . ($columnName === 'content' ? ' [role="combobox"]' : ' button[aria-haspopup="listbox"]');
        $component = 'Livewire.find(document.querySelector("[data-testid=editable-title]").closest("[wire\\\\:id]").getAttribute("wire:id"))';

        // Defer the permission change until the save to exercise a stale, enabled control.
        $page->script($component . '.set("saveScenario", "disabled", false)');
        $page->click($control)->click($column . ' [role="option"][data-value="published"]')
            ->assertAttribute($control, 'aria-invalid', 'true')
            ->assertDisabled($control)
            ->assertAttribute($control, 'aria-disabled', 'true')
            ->assertValue($column . ' input[x-ref="serverState"]', 'draft')
            ->assertSeeIn($control, 'Draft')
            ->assertNoAccessibilityIssues();

        expect(data_get($post->fresh(), $columnName))->toBe('draft');

        $page->script($component . '.set("saveScenario", "success")');
        $page->assertEnabled($control)
            ->click($control)->click($column . ' [role="option"][data-value="published"]')
            ->assertValue($column . ' input[x-ref="serverState"]', 'published')
            ->assertSeeIn($control, 'Published')
            ->assertAttributeMissing($control, 'aria-invalid')
            ->assertPresent($control . ':focus')
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();

        expect(data_get($post->fresh(), $columnName))->toBe('published');
    }
})->with(['content', 'json.status']);

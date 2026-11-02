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

it('renders the configured cells instead of the whole table after `updateTableColumnState()`', function (string $saveScenario, array $expectedPartiallyRenderedColumns): void {
    $post = Post::factory()->create(['title' => 'Original title', 'rating' => 1]);
    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true, 'saveScenario' => $saveScenario])
        ->call('updateTableColumnState', 'title', (string) $post->getKey(), 'mixedCase');

    $expectedPartialNames = array_map(
        fn (string $columnName): string => "table.record.{$post->getKey()}.column.{$columnName}",
        $expectedPartiallyRenderedColumns,
    );

    expect($post->fresh())->title->toBe('MIXEDCASE')->rating->toBe(2)
        ->and($component->effects)->not->toHaveKey('html')
        ->and(array_keys($component->effects['partials']))->toBe($expectedPartialNames)
        ->and($component->effects['partials']["table.record.{$post->getKey()}.column.title"])->toContain('value="MIXEDCASE"');

    $partials = $component->effects['partials'];

    // The full table renders the same cell HTML, so the partial can replace the cell in place.
    $component->call('$refresh');

    foreach ($partials as $partialHtml) {
        expect($component->effects['html'])->toContain($partialHtml);
    }
})->with([
    '`partiallyRenderAfterStateUpdated()`' => ['partial', ['title']],
    '`partiallyRenderColumnsAfterStateUpdated()`' => ['partial-columns', ['title', 'rating']],
]);

it('renders the whole table after `updateTableColumnState()` by default', function (): void {
    $post = Post::factory()->create(['title' => 'Original title']);
    $component = livewire(ColumnsBrowserTest::class, ['editableColumns' => true])
        ->call('updateTableColumnState', 'title', (string) $post->getKey(), 'Updated title');

    expect($component->effects['html'])->toContain('value="Updated title"')->not->toContain('wire:partial="table.record.')
        ->and($component->effects['partials'] ?? [])->toBe([]);
});

describe('browser interactions', function (): void {
    beforeEach(function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());
    });

    it('describes validation errors at the focus owner and clears them on correction', function (): void {
        $post = Post::factory()->create();

        foreach ([false, true] as $isDarkMode) {
            $post->refresh()->update(['is_published' => false, 'title' => 'Draft title', 'rating' => 1, 'content' => 'draft', 'json' => ['approved' => false, 'status' => 'draft']]);
            $page = visit('/columns-browser-test?editableColumns=1&saveScenario=validation');

            if ($isDarkMode) {
                $page = $page->inDarkMode();
            }

            foreach ([
                'is_published' => 'input[type="checkbox"]',
                'json-approved' => '[role="switch"]',
                'title' => 'input:not([type="hidden"])',
                'rating' => 'select',
                'content' => '[role="combobox"]',
                'json-status' => 'button[aria-haspopup="listbox"]',
            ] as $name => $controlSelector) {
                $column = '[data-testid="editable-' . $name . '"]';
                $control = $column . ' ' . $controlSelector;

                if ($name === 'title') {
                    $page->assertValue($control, 'Draft title')
                        ->fill($control, 'Updated title')->keys($control, 'Tab');
                } elseif ($name === 'rating') {
                    $page->select($control, '2');
                } elseif ($name === 'content') {
                    $page->keys($control, ['Enter', 'ArrowDown', 'Enter']);
                } elseif ($name === 'json-status') {
                    $page->click($control)->fill($column . ' input[role="combobox"]', 'pub')
                        ->keys($column . ' input[role="combobox"]', ['ArrowDown', 'Enter']);
                } else {
                    $page->click($control);
                }

                $page->assertAttribute($control, 'aria-invalid', 'true');

                if ($isDarkMode) {
                    continue;
                }

                $encodedControl = json_encode($control);
                $page->assertScript(<<<JS
                        (() => {
                            const control = document.querySelector({$encodedControl})
                            return control.getAttribute('aria-describedby').split(' ').some(id => {
                                const description = document.getElementById(id)
                                return description?.checkVisibility() && description.textContent.trim() !== '' && id !== 'editable-column-help'
                            })
                        })()
                        JS, true);

                if ($name !== 'json-approved') {
                    $page->assertAttributeContains($control, 'aria-describedby', 'editable-column-help');
                }
            }

            if ($isDarkMode) {
                $page->assertNoAccessibilityIssues();

                continue;
            }

            expect($post->fresh())->is_published->toBeFalse()->title->toBe('Draft title')->rating->toBe(1)->content->toBe('draft');
            expect($post->fresh()->json)->approved->toBeFalse()->status->toBe('draft');

            $page->assertNoAccessibilityIssues()
                ->fill('[data-testid="editable-title"] input:not([type="hidden"])', 'Corrected title')
                ->keys('[data-testid="editable-title"] input:not([type="hidden"])', 'Tab')
                ->assertValue('[data-testid="editable-title"] input:not([type="hidden"])', 'CORRECTED TITLE')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-invalid')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-busy')
                ->assertAttributeContains('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-describedby', 'editable-column-help')
                ->assertNoSmoke();

            expect($post->fresh()->title)->toBe('CORRECTED TITLE');
        }
    });

    it('displays persisted state after save hooks and `void` callbacks', function (string $scenario, string $expectedState): void {
        $post = Post::factory()->create(['title' => 'Original title']);

        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

        $control = '[data-testid="editable-title"] input:not([type="hidden"])';
        $page->assertValue($control, 'Original title')
            ->fill($control, 'mixedCase')
            ->keys($control, 'Tab')
            ->assertValue($control, $expectedState)
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertNoSmoke();

        expect($post->fresh()->title)->toBe($expectedState);
    })->with([
        'normalization' => ['normalize', 'MIXEDCASE'],
        'custom records' => ['custom-records', 'MIXEDCASE'],
    ]);

    it('updates partially rendered cells without rendering the rest of the table', function (): void {
        $post = Post::factory()->create(['title' => 'Original title', 'rating' => 1]);

        $page = visit('/columns-browser-test?editableColumns=1&saveScenario=partial-columns');

        // Rendering the whole table would morph this cell and remove the attribute added in the browser.
        $page->script(<<<'JS'
            document.querySelector('[data-testid="editable-is_published"]').closest('td').setAttribute('data-testid', 'unrendered-cell')
            JS);

        $control = '[data-testid="editable-title"] input:not([type="hidden"])';
        $page->assertValue($control, 'Original title')
            ->fill($control, 'mixedCase')
            ->keys($control, 'Tab')
            ->assertValue($control, 'MIXEDCASE')
            ->assertValue('[data-testid="editable-rating"] select', '2')
            ->assertAttributeMissing($control, 'aria-busy')
            ->assertPresent('[data-testid="unrendered-cell"]')
            ->assertNoSmoke();

        expect($post->fresh())->title->toBe('MIXEDCASE')->rating->toBe(2);
    });
});

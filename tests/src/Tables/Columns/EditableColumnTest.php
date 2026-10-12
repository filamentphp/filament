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

                $encodedControl = json_encode($control);
                $page->assertAttribute($control, 'aria-invalid', 'true')
                    ->assertScript(<<<JS
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

            expect($post->fresh())->is_published->toBeFalse()->title->toBe('Draft title')->rating->toBe(1)->content->toBe('draft');
            expect($post->fresh()->json)->approved->toBeFalse()->status->toBe('draft');

            $page->assertNoAccessibilityIssues()
                ->click('[data-testid="allow-column-edits"]')
                ->fill('[data-testid="editable-title"] input:not([type="hidden"])', 'Corrected title')
                ->keys('[data-testid="editable-title"] input:not([type="hidden"])', 'Tab')
                ->assertValue('[data-testid="editable-title"] input:not([type="hidden"])', 'Corrected title')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-invalid')
                ->assertAttributeMissing('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-busy')
                ->assertAttributeContains('[data-testid="editable-title"] input:not([type="hidden"])', 'aria-describedby', 'editable-column-help')
                ->assertNoAccessibilityIssues()
                ->assertNoSmoke();

            expect($post->fresh()->title)->toBe('Corrected title');
        }
    });

    it('displays persisted state after save hooks and `void` callbacks', function (string $scenario, string $expectedState): void {
        $post = Post::factory()->create(['title' => 'Original title']);

        foreach ([false, true] as $isDarkMode) {
            $post->refresh()->update(['title' => 'Original title']);
            $page = visit('/columns-browser-test?editableColumns=1&saveScenario=' . $scenario);

            if ($isDarkMode) {
                $page = $page->inDarkMode();
            }

            $control = '[data-testid="editable-title"] input:not([type="hidden"])';
            $page->assertValue($control, 'Original title')
                ->fill($control, 'mixedCase')
                ->keys($control, 'Tab')
                ->assertAttributeMissing($control, 'aria-busy')
                ->assertValue($control, $expectedState)
                ->assertNoAccessibilityIssues()
                ->assertNoSmoke();

            expect($post->fresh()->title)->toBe($expectedState);
        }
    })->with([
        'normalization' => ['normalize', 'MIXEDCASE'],
        'cached column state' => ['cached-state', 'mixedCase'],
        'custom records' => ['custom-records', 'MIXEDCASE'],
    ]);
});

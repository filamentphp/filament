<?php

namespace Filament\Tests\Forms;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('partial rendering', function (): void {
    it('can use `partiallyRenderComponentsAfterStateUpdated()` to re-render only the specified fields', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            $productName = fake()->sentence;

            $page = visit('/partial-rendering-test');

            $productSku = $page->text('.product-sku');

            $page
                ->assertSee('Product SKU')
                ->assertSee('Product Name')
                ->fill('#form\.product_name', $productName)
                ->wait(3)
                ->assertSee($productSku)
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });

    it('can use `partiallyRenderAfterStateUpdated()` to re-render only the current component', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            $page = visit('/partial-rendering-test');

            $postTitle = fake()->sentence;
            $postDate = $page->text('.post-date');

            $page
                ->assertSee('Post Title')
                ->assertSee('Post Date')
                ->fill('#form\.post_title', $postTitle)
                ->wait(1)
                ->assertSee('/' . Str::slug($postTitle))
                ->assertSee($postDate)
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });

    it('can use `skipRenderAfterStateUpdated()` to prevent the Livewire component from re-rendering when a field is updated', function (): void {
        retry(10, callback: function (): void {
            $this->actingAs(User::factory()->create());

            $page = visit('/partial-rendering-test');

            $question = $page->text('.question .fi-fo-field-label-content');
            $answer = (string) fake()->numberBetween(1, 5);

            $page
                ->assertSee($question)
                ->radio("#form\.question-{$answer}", $answer)
                ->wait(1)
                ->assertSee($question)
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });

    it('has no accessibility issues in dark mode', function (): void {
        retry(10, callback: function (): void {
            $this->actingAs(User::factory()->create());

            visit('/partial-rendering-test')
                ->inDarkMode()
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });
});

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
                ->assertValue('#form\.product_slug', Str::slug($productName))
                ->assertSee($productSku)
                ->assertNoSmoke();
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
                ->assertSee('/' . Str::slug($postTitle))
                ->assertSee($postDate)
                ->assertNoSmoke();
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
                ->waitForEvent('networkidle')
                ->assertSee($question)
                ->assertNoSmoke();
        });
    });
});

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
    it('can partially render specified fields, the current component, and skip rendering after state updates', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            $productName = fake()->sentence;

            $page = visit('/partial-rendering-test');

            $productSku = $page->text('.product-sku');

            $page
                ->fill('#form\.product_name', $productName)
                ->assertValue('#form\.product_slug', Str::slug($productName))
                ->assertSee($productSku)
                ->assertNoSmoke();

            $postTitle = fake()->sentence;
            $postDate = $page->text('.post-date');

            $page
                ->fill('#form\.post_title', $postTitle)
                ->assertSee('/' . Str::slug($postTitle))
                ->assertSee($postDate)
                ->assertNoSmoke();

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

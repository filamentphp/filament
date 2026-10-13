<?php

namespace Filament\Tests\Schemas\Components\Concerns;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can partially render specified fields and the current component after state updates', function (): void {
    $this->actingAs(User::factory()->create());

    $productName = 'Summer linen shirt';

    $page = visit('/partial-rendering-test');

    $productSku = $page->text('.product-sku');

    $page
        ->fill('#form\.product_name', $productName)
        ->assertValue('#form\.product_slug', Str::slug($productName))
        ->assertSee($productSku)
        ->assertNoSmoke();

    $postTitle = 'A guide to summer travel';
    $postDate = $page->text('.post-date');

    $page
        ->fill('#form\.post_title', $postTitle)
        ->assertSee('/' . Str::slug($postTitle))
        ->assertSee($postDate)
        ->assertNoSmoke();
});

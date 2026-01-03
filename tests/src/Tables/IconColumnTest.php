<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnStaticUrl;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnUrl;
use Filament\Tests\Tables\Fixtures\PostsTableWithIconColumnUrlNewTab;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render icon column with state-based URL', function (): void {
    Post::factory()->create();

    livewire(PostsTableWithIconColumnUrl::class)
        ->assertSeeHtml('href="https://example.com/icon-link"');
});

it('can render icon column with static URL', function (): void {
    Post::factory()->create();

    livewire(PostsTableWithIconColumnStaticUrl::class)
        ->assertSeeHtml('href="https://example.com/static-link"');
});

it('can render icon column with URL that opens in new tab', function (): void {
    Post::factory()->create();

    livewire(PostsTableWithIconColumnUrlNewTab::class)
        ->assertSeeHtml('target="_blank"');
});

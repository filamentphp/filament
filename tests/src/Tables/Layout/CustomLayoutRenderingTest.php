<?php

use Filament\Tests\Fixtures\Livewire\PostsTableWithIncompleteLayout;
use Filament\Tests\Fixtures\Livewire\PostsTableWithStubLayout;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\View\ViewException;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders a custom layout inside the table wrapper and frame', function (): void {
    Post::factory()->count(2)->create();

    $html = livewire(PostsTableWithStubLayout::class)->html();

    expect($html)
        ->toContain('x-data="filamentTable(')
        ->toContain('class="fi-ta-ctn')
        ->toContain('<div class="fi-ta-main">')
        ->toContain('<p>stub content</p>')
        ->not->toContain('fi-sc');
});

it('renders a custom layout without the frame when `contained(false)`', function (): void {
    Post::factory()->count(2)->create();

    $html = livewire(PostsTableWithStubLayout::class, ['contained' => false])->html();

    expect($html)
        ->toContain('x-data="filamentTable(')
        ->toContain('<p>stub content</p>')
        ->not->toContain('fi-ta-ctn')
        ->not->toContain('fi-ta-main');
});

it('throws when a custom layout has no `TableContent` part', function (): void {
    livewire(PostsTableWithIncompleteLayout::class);
})->throws(ViewException::class, 'does not contain a [Filament\Tables\Components\TableContent] part');

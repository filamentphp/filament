<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('does not leak a mounted action\'s record into other rows\' grouped action visibility', function () {
    $visiblePost = Post::factory()->create(['is_published' => true]);
    $hiddenPost = Post::factory()->create(['is_published' => false]);

    // `groupedConditional` requires confirmation, so mounting it for
    // $visiblePost leaves its record "stuck" on that specific action object
    // (see Filament\Tables\Table\Concerns\HasActions::getAction()) while the
    // modal is open and the whole table re-renders.
    $html = livewire(PostsTable::class)
        ->mountTableAction('groupedConditional', $visiblePost)
        ->html();

    // `groupedConditional` is visible only for $visiblePost, so its trigger
    // (which embeds the acting record's key) must appear exactly once. Before
    // the fix, the stale $visiblePost record leaked into $hiddenPost's row
    // too, rendering a second, wrongly-visible copy that pointed at the
    // wrong record.
    expect(substr_count($html, "mountTableAction('groupedConditional', '{$visiblePost->getKey()}')"))
        ->toBe(1);
});

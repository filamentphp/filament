<?php

use Filament\Tables\Components\TableContent;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the records inside a custom layout', function (): void {
    $posts = Post::factory()->count(3)->create();

    $html = livewireTableWithParts([TableContent::make()])->html();

    expect($html)
        ->toContain('class="fi-ta-content-ctn fi-fixed-positioning-context"')
        ->toContain('class="fi-ta-table"');

    foreach ($posts as $post) {
        expect($html)->toContain(".table.records.{$post->getKey()}\"");
    }
});

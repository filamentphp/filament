<?php

use Filament\Tables\Components\TableEmptyState;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the heading and actions for an empty result', function (): void {
    $html = livewireTableWithParts(
        [TableEmptyState::make()],
        fn (Table $table): Table => $table
            ->emptyStateHeading('No posts yet')
            ->emptyStateDescription('Create your first post.'),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-empty-state"')
        ->toContain('No posts yet')
        ->toContain('Create your first post.')
        ->toContain('mountAction(\'emptyExists\'');
});

it('renders nothing when records exist', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts([TableEmptyState::make()])->html();

    expect($html)->not->toContain('fi-ta-empty-state');
});

it('renders a custom `emptyState()` view', function (): void {
    $html = livewireTableWithParts(
        [TableEmptyState::make()],
        fn (Table $table): Table => $table->emptyState(new HtmlString('<p>custom empty state</p>')),
    )->html();

    expect($html)
        ->toContain('<p>custom empty state</p>')
        ->not->toContain('fi-ta-empty-state');
});

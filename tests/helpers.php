<?php

namespace Filament\Tests;

use Closure;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\ConfigurablePostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithParts;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

if (! function_exists('\Filament\Tests\livewire')) {
    function livewire(string | Component $component, array $props = []): Testable
    {
        return Livewire::test($component, $props);
    }
}

if (! function_exists('\Filament\Tests\normalizeTableHtml')) {
    /**
     * Makes rendered table HTML comparable between runs: the Livewire ID and the
     * `Str::random()` `wire:key` values are replaced with fixed tokens, Livewire's
     * snapshot payload is removed, and whitespace is collapsed so that indentation
     * changes between views do not affect the result.
     */
    function normalizeTableHtml(string $html, string $livewireId): string
    {
        $html = str_replace($livewireId, '__LIVEWIRE_ID__', $html);
        $html = preg_replace('/\s+wire:(snapshot|effects)="[^"]*"/', '', $html);
        $html = preg_replace('/(bulk-select-page\.checkbox(?:\.stacked)?\.)[A-Za-z0-9]+/', '$1__RANDOM__', $html);
        $html = preg_replace('/wire:key="[A-Za-z0-9]{16}"/', 'wire:key="__RANDOM__"', $html);
        // The root key belongs to the test harness: Livewire composes it from static loop state left by earlier renders.
        $html = preg_replace('/wire:key="lw-[^"]*"/', 'wire:key="__ROOT__"', $html, limit: 1);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/\s+>/', '>', $html);
        $html = preg_replace('/>\s+</', '><', $html);

        return trim($html);
    }
}

if (! function_exists('\Filament\Tests\seedPostsForTableSnapshot')) {
    /**
     * Snapshots need identical data on every run, so the records are created with
     * fixed attributes instead of Faker output.
     */
    function seedPostsForTableSnapshot(): void
    {
        $author = User::factory()->create([
            'name' => 'Dan Harrin',
            'email' => 'dan@filamentphp.com',
            'remember_token' => 'remember-token',
        ]);

        foreach (['Alpha', 'Bravo', 'Charlie'] as $index => $title) {
            Post::factory()->create([
                'author_id' => $author,
                'content' => "{$title} content",
                'is_published' => ($index % 2) === 0,
                'rating' => $index + 1,
                'sort' => $index,
                'tags' => ['tag'],
                'title' => "{$title} post",
            ]);
        }
    }
}

if (! function_exists('\Filament\Tests\renderDefaultTableLayout')) {
    /**
     * Renders a table variant from `DefaultLayoutVariants` and returns its
     * normalised HTML. With `$usingLegacyView`, the table renders through the
     * pre-refactor monolithic view instead of the layout parts.
     */
    function renderDefaultTableLayout(string $component, ?Closure $configureTable = null, ?Closure $interact = null, bool $usingLegacyView = false): string
    {
        ConfigurablePostsTable::$configureTable = $configureTable;

        return Table::configureUsing(
            fn (Table $table): Table => $usingLegacyView ? $table->view('fixtures.legacy-table') : $table,
            during: function () use ($component, $interact): string {
                $livewire = livewire($component);

                if ($interact) {
                    $interact($livewire);
                }

                return normalizeTableHtml($livewire->html(), $livewire->instance()->getId());
            },
        );
    }
}

if (! function_exists('\Filament\Tests\livewireTableWithParts')) {
    /**
     * @param  array<\Filament\Schemas\Components\Component>  $parts
     */
    function livewireTableWithParts(array $parts, ?Closure $configureTable = null): Testable
    {
        PostsTableWithParts::$parts = $parts;
        PostsTableWithParts::$configureTable = $configureTable;

        return livewire(PostsTableWithParts::class);
    }
}

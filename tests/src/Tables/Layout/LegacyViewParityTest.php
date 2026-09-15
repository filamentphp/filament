<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Tables\DefaultLayoutVariants;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\renderDefaultTableLayout;
use function Filament\Tests\seedPostsForTableSnapshot;

uses(TestCase::class);

/**
 * The layout parts must produce the markup of the monolithic view they replaced.
 * `fixtures/legacy-table.blade.php` is that view, kept verbatim, so every
 * variant is rendered through both and compared. A published copy of the old
 * view keeps working for the same reason.
 */
it('renders the same markup as the legacy view', function (string $component, ?Closure $configureTable, ?Closure $interact): void {
    seedPostsForTableSnapshot();

    $legacyHtml = renderDefaultTableLayout($component, $configureTable, $interact, usingLegacyView: true);

    expect(renderDefaultTableLayout($component, $configureTable, $interact))->toBe($legacyHtml);
})->with(DefaultLayoutVariants::all());

it('fires every render hook where the legacy view did', function (): void {
    seedPostsForTableSnapshot();

    foreach (array_unique((new ReflectionClass(TablesRenderHook::class))->getConstants()) as $hook) {
        FilamentView::registerRenderHook($hook, fn (): string => "<div class=\"render-hook\">{$hook}</div>");
    }

    $legacyHtml = renderDefaultTableLayout(PostsTable::class, usingLegacyView: true);

    expect($legacyHtml)->toContain('tables::toolbar.start');
    expect(renderDefaultTableLayout(PostsTable::class))->toBe($legacyHtml);
});

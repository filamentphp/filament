<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Tables\DefaultLayoutVariants;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\renderDefaultTableLayout;
use function Filament\Tests\seedPostsForTableSnapshot;

uses(TestCase::class);

it('renders the default layout', function (string $component, ?Closure $configureTable, ?Closure $interact): void {
    seedPostsForTableSnapshot();

    expect(renderDefaultTableLayout($component, $configureTable, $interact))->toMatchSnapshot();
})->with(DefaultLayoutVariants::all());

it('renders the default layout with every render hook', function (): void {
    seedPostsForTableSnapshot();

    foreach (array_unique((new ReflectionClass(TablesRenderHook::class))->getConstants()) as $hook) {
        FilamentView::registerRenderHook($hook, fn (): string => "<div class=\"render-hook\">{$hook}</div>");
    }

    expect(renderDefaultTableLayout(PostsTable::class))->toMatchSnapshot();
});

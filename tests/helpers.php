<?php

namespace Filament\Tests;

use Closure;
use Filament\Tests\Fixtures\Livewire\PostsTableWithParts;
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
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/>\s+</', '><', $html);

        return trim($html);
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

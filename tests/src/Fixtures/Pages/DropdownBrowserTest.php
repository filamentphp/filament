<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DropdownBrowserTest extends Page
{
    protected string $view = 'pages.dropdown-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static bool $shouldRegisterNavigation = false;

    /**
     * @var array<int, string>
     */
    public array $items = [];

    public int $prependedCount = 0;

    public function mount(): void
    {
        $this->items = collect(range(1, 25))
            ->map(static fn (int $itemNumber): string => "item-{$itemNumber}")
            ->all();
    }

    /**
     * Prepends a row and drops the last one, so every retained keyed row moves in the
     * morph, like a timeline capped at a fixed number of entries.
     */
    public function prependItem(): void
    {
        $this->prependedCount++;

        $this->items = array_slice(["new-{$this->prependedCount}", ...$this->items], 0, 25);
    }
}

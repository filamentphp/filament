<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SidebarWidthBrowserTest extends Page
{
    protected string $view = 'pages.sidebar-width-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public function mount(): void
    {
        filament()->getCurrentPanel()
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth(request()->boolean('customWidth') ? '3rem' : '4.5rem');
    }
}

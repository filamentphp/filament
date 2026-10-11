<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Tests\Fixtures\Widgets\ChartWidgetWithAssistiveContent;

class ChartWidgetBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderWidgets(): array
    {
        return [
            ChartWidgetWithAssistiveContent::class,
        ];
    }
}

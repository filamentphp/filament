<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Tests\Fixtures\Widgets\StatsOverviewWidgetWithPlaceholder;

class StatsOverviewWidgetBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidgetWithPlaceholder::class,
        ];
    }
}

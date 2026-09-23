<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class SelectBrowserTest extends Page
{
    protected string $view = 'pages.select-browser-test';

    protected static bool $shouldRegisterNavigation = false;
}

<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class LoadingIndicatorBrowserTest extends Page
{
    protected string $view = 'pages.loading-indicator-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getIndicatorCases(): array
    {
        return [
            [],
            ['size' => 'xs'],
            ['size' => 'sm'],
            ['size' => 'md'],
            ['size' => 'lg'],
            ['size' => 'xl'],
            ['size' => '2xl', 'class' => 'custom-indicator', 'aria-hidden' => 'false', 'role' => 'img', 'aria-label' => 'Loading content', 'data-state' => 'waiting'],
        ];
    }
}

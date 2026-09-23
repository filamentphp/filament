<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class LoadingSectionBrowserTest extends Page
{
    protected string $view = 'pages.loading-section-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getLoadingSectionCases(): array
    {
        return [
            [],
            ['height' => '12rem', 'loadingLabel' => 'Loading projects', 'class' => 'custom', 'title' => 'Projects'],
            ['columnSpan' => 2, 'columnStart' => 3],
            ['columnSpan' => ['default' => 1, 'lg' => 2], 'columnStart' => ['default' => 1, 'lg' => 3]],
            ['height' => null, 'loadingLabel' => null, 'columnSpan' => null],
            ['loadingLabel' => '', 'columnSpan' => ['sm' => 0, 'lg' => null]],
            ['columnSpan' => ['default' => 'hidden']],
            ['columnSpan' => ['default' => 1, '@sm' => 2], 'columnStart' => ['default' => 1, '@sm' => 3]],
            ['columnStart' => '2e0'],
        ];
    }
}

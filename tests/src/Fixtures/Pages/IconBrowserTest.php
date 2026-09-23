<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class IconBrowserTest extends Page
{
    protected string $view = 'pages.icon-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getIconCases(): array
    {
        return [
            [],
            ['size' => 'xs'],
            ['size' => 'sm'],
            ['size' => 'md'],
            ['size' => 'lg'],
            ['size' => 'xl'],
            ['size' => '2xl', 'class' => 'custom-icon', 'role' => 'img', 'aria-label' => 'Saved', 'data-state' => 'waiting'],
            ['src' => '/icon-browser-test.svg', 'loading' => 'lazy'],
            ['src' => '/icon-browser-test.svg', 'alt' => 'Saved image', 'size' => 'lg'],
            ['empty' => true],
        ];
    }
}

<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class BreadcrumbsBrowserTest extends Page
{
    protected string $view = 'pages.breadcrumbs-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getBreadcrumbCases(): array
    {
        return [
            ['aria-label' => 'Location', 'class' => 'location-trail', 'title' => 'Account location', 'breadcrumbs' => [
                ['label' => 'Home', 'href' => '#home'],
                ['label' => 'Users & teams', 'href' => '#users'],
                ['label' => '<Create user>'],
            ]],
            ['aria-label' => 'Linked location', 'breadcrumbs' => [
                ['label' => 'Home', 'href' => '#home'],
                ['label' => 'Users', 'href' => '#users'],
            ]],
            ['aria-label' => 'Single location', 'breadcrumbs' => [['label' => 'Home']]],
            ['aria-label' => 'Empty location'],
            ['aria-label' => 'Custom location', 'custom' => true, 'breadcrumbs' => [
                ['label' => 'Home', 'href' => '#home'],
                ['label' => 'Settings'],
            ]],
        ];
    }
}

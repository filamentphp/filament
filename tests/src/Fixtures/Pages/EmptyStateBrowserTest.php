<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class EmptyStateBrowserTest extends Page
{
    protected string $view = 'pages.empty-state-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getEmptyStateCases(): array
    {
        return [
            ['heading' => '<Projects>', 'description' => 'Start a project', 'withActions' => true, 'title' => 'Projects', 'class' => 'projects'],
            ['heading' => 'Projects', 'withIcon' => true, 'withActions' => true, 'rich' => true],
            ['heading' => 'Projects', 'description' => '0', 'footer' => '0', 'compact' => true, 'contained' => false, 'headingTag' => 'h3', 'withIcon' => true, 'iconColor' => 'gray', 'iconSize' => 'sm'],
            ['heading' => 'Projects', 'description' => '  ', 'footer' => '  '],
            ['heading' => 'Projects', 'withIcon' => true, 'iconColor' => 'success', 'iconSize' => 'xl'],
        ];
    }
}

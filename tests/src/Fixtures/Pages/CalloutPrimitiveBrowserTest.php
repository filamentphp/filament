<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class CalloutPrimitiveBrowserTest extends Page
{
    protected string $view = 'pages.callout-primitive-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getCalloutCases(): array
    {
        return [
            ['heading' => '<Projects>', 'description' => 'Start a project', 'withActions' => true, 'title' => 'Projects', 'class' => 'projects'],
            ['heading' => 'Projects', 'withIcon' => true, 'withActions' => true, 'rich' => true, 'color' => 'info', 'controls' => 'Details'],
            ['heading' => 'Projects', 'description' => '0', 'footer' => '0', 'withIcon' => true, 'color' => 'warning', 'iconColor' => 'gray', 'iconSize' => 'sm'],
            ['heading' => ' ', 'description' => '  ', 'footer' => '  ', 'controls' => '  '],
            ['description' => 'Ready', 'withIcon' => true, 'color' => 'danger', 'iconColor' => 'success', 'iconSize' => 'xl'],
            ['footer' => 'Continue', 'color' => 'primary'],
            ['controls' => 'Help', 'color' => 'brand', 'withIcon' => true],
            ['heading' => 'Saved', 'color' => 'success', 'withIcon' => true],
        ];
    }
}

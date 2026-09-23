<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class BadgePrimitiveBrowserTest extends Page
{
    protected string $view = 'pages.badge-primitive-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getBadgeCases(): array
    {
        return [[], ['color' => 'gray', 'size' => 'xs'], ['color' => 'brand', 'size' => 'sm'], ['tag' => 'a', 'href' => '#badge-destination'], ['tag' => 'button', 'type' => 'submit', 'form' => 'badge-form'], ['deletable' => true, 'deleteLabel' => 'Remove priority']];
    }
}

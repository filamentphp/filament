<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class ActionsPrimitiveBrowserTest extends Page
{
    protected string $view = 'pages.actions-primitive-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getActionsCases(): array
    {
        return [
            [],
            ['alignment' => null],
            ['alignment' => 'left'],
            ['alignment' => 'center'],
            ['alignment' => 'end'],
            ['alignment' => 'right'],
            ['alignment' => 'between'],
            ['alignment' => 'justify'],
            ['alignment' => 'custom-layout', 'class' => 'custom', 'title' => 'Actions'],
            ['alignment' => ''],
            ['alignment' => 'right', 'fullWidth' => true],
        ];
    }
}

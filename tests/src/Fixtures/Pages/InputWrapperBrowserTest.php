<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class InputWrapperBrowserTest extends Page
{
    protected string $view = 'pages.input-wrapper-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getWrapperCases(): array
    {
        return [
            [],
            ['prefix' => '£'],
            ['suffix' => 'GBP', 'inlineSuffix' => true],
            ['prefix' => 'https://', 'suffix' => '.com', 'inlinePrefix' => true, 'valid' => false],
            ['icons' => true, 'disabled' => true],
            ['icons' => true, 'prefix' => '£', 'suffix' => 'GBP', 'inlinePrefix' => true, 'inlineSuffix' => true],
            ['rich' => true],
            ['prefix' => '  ', 'suffix' => ''],
        ];
    }
}

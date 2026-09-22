<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class FieldsetBrowserTest extends Page
{
    protected string $view = 'pages.fieldset-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getFieldsetCases(): array
    {
        return [
            ['label' => '<Address>', 'class' => 'address-fields', 'name' => 'address', 'form' => 'profile', 'title' => 'Postal address'],
            ['label' => 'Required address', 'required' => true],
            ['label' => 'Hidden address', 'labelHidden' => true, 'contained' => false, 'disabled' => true],
            [],
            ['label' => '  ', 'required' => true],
            ['label' => '0'],
            ['rich' => true, 'required' => true],
        ];
    }
}

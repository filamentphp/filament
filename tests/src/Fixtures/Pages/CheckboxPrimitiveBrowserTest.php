<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class CheckboxPrimitiveBrowserTest extends Page
{
    protected string $view = 'pages.checkbox-primitive-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getCheckboxCases(): array
    {
        return [
            ['name' => 'unchecked', 'value' => 'yes', 'class' => 'custom-checkbox', 'data-state' => 'initial', 'autocomplete' => 'off'],
            ['name' => 'initial', 'value' => 'saved', 'defaultChecked' => true],
            ['name' => 'disabled', 'value' => 'excluded', 'defaultChecked' => true, 'disabled' => true],
            ['name' => 'required', 'required' => true],
            ['name' => 'mixed', 'indeterminate' => true],
        ];
    }
}

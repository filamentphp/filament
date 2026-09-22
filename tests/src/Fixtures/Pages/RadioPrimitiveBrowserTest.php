<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;

class RadioPrimitiveBrowserTest extends Page
{
    protected string $view = 'pages.radio-primitive-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    /** @return array<array<string, mixed>> */
    public function getRadioCases(): array
    {
        return [
            ['name' => 'delivery', 'value' => 'standard', 'defaultChecked' => true, 'class' => 'custom-radio', 'data-state' => 'initial', 'autocomplete' => 'off'],
            ['name' => 'delivery', 'value' => 'express'],
            ['name' => 'separate', 'value' => 'saved', 'defaultChecked' => true],
            ['name' => 'disabled', 'value' => 'excluded', 'defaultChecked' => true, 'disabled' => true],
            ['name' => 'required', 'required' => true],
        ];
    }
}

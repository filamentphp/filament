<?php

namespace Filament\Tests;

use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

if (! function_exists('\Filament\Tests\livewire')) {
    function livewire(string $component, array $props = []): Testable
    {
        return Livewire::test($component, $props);
    }
}

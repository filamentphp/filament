<?php

namespace Filament\Tests;

use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

if (! function_exists('\Filament\Tests\livewire')) {
    function livewire(string | Component $component, array $props = []): Testable
    {
        return Livewire::test($component, $props);
    }
}

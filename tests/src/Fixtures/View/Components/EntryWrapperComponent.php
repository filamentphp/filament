<?php

namespace Filament\Tests\Fixtures\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EntryWrapperComponent extends Component
{
    public function render(): View
    {
        return view('fixtures.entry-wrapper-blade-component');
    }
}

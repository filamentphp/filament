<?php

namespace Filament\Tests\Fixtures\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FieldWrapperComponent extends Component
{
    public function render(): View
    {
        return view('fixtures.field-wrapper-blade-component');
    }
}

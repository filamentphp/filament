<?php

namespace Filament\Forms;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

abstract class FormsComponent extends Component implements Contracts\HasForms, HasActions
{
    use Concerns\InteractsWithForms;
    use InteractsWithActions;
}

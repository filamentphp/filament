<?php

namespace Filament\Forms;

use Livewire\Component;

abstract class FormsComponent extends Component implements Contracts\HasForms
{
    use Concerns\InteractsWithForms;
}

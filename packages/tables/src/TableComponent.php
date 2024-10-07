<?php

namespace Filament\Tables;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Livewire\Component;

abstract class TableComponent extends Component implements Contracts\HasTable, Forms\Contracts\HasForms, HasActions
{
    use Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
    use InteractsWithActions;
}

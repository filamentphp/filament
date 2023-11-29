<?php

namespace Filament\Tables;

use Filament\Forms;
use Livewire\Component;

abstract class TableComponent extends Component implements Contracts\HasTable, Forms\Contracts\HasForms
{
    use Concerns\InteractsWithTable;
    use Forms\Concerns\InteractsWithForms;
}

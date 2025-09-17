<?php

namespace Filament\Tables;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

abstract class TableComponent extends Component implements Contracts\HasTable, HasActions, HasSchemas
{
    use Concerns\InteractsWithTable;
    use InteractsWithActions;
    use InteractsWithSchemas;
}

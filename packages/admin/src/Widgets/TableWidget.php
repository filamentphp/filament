<?php

namespace Filament\Widgets;

use Filament\Tables;

class TableWidget extends Widget implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament::widgets.table-widget';
}

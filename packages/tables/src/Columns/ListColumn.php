<?php

namespace Filament\Tables\Columns;

use BackedEnum;
use Closure;
use Filament\Support\Contracts\HasLabel as LabelInterface;
use Illuminate\Contracts\Support\Arrayable;
use stdClass;

class ListColumn extends Column
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.list-column';
}

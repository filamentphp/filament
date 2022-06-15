<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @deprecated use Select with the `multiple()` and `relationship()` methods instead.
 */
class BelongsToManyMultiSelect extends MultiSelect
{
}

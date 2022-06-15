<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @deprecated use Select with the `relationship()` method instead.
 */
class BelongsToSelect extends Select
{
}

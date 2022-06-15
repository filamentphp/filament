<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;

/**
 * @deprecated use Repeater with the `relationship()` method instead.
 */
class RelationshipRepeater extends Repeater
{
}

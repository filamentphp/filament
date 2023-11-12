<?php

namespace Filament\Forms\Components\Contracts;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

interface CanDisableOptions
{
    public function disableOptionWhen(bool | Closure $callback): static;
}

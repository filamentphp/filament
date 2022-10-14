<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeHasManyThroughCommand extends Commands\MakeHasManyThroughCommand
{
    protected $hidden = true;

    protected $signature = 'filament:has-many-through {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}

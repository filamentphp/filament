<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeBelongsToManyCommand extends Commands\MakeBelongsToManyCommand
{
    protected $hidden = true;

    protected $signature = 'filament:belongs-to-many {resource?} {relationship?} {recordTitleAttribute?} {--F|force}';
}

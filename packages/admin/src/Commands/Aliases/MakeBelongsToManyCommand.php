<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeBelongsToManyCommand extends Commands\MakeHasManyCommand
{
    protected $hidden = true;

    protected $signature = 'filament:belongs-to-many {resource?} {relationship?} {recordTitleAttribute?}';
}

<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeMorphManyCommand extends Commands\MakeHasManyCommand
{
    protected $hidden = true;

    protected $signature = 'filament:morph-many {resource?} {relationship?} {recordTitleAttribute?}';
}

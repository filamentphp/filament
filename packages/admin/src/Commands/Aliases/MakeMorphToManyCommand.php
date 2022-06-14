<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeMorphToManyCommand extends Commands\MakeMorphToManyCommand
{
    protected $hidden = true;

    protected $signature = 'filament:morph-to-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}

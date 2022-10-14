<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeMorphManyCommand extends Commands\MakeMorphManyCommand
{
    protected $hidden = true;

    protected $signature = 'filament:morph-many {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--F|force}';
}

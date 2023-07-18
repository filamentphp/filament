<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeRelationManagerCommand extends Commands\MakeRelationManagerCommand
{
    protected $hidden = true;

    protected $signature = 'filament:relation-manager {resource?} {relationship?} {recordTitleAttribute?} {--attach} {--associate} {--soft-deletes} {--view} {--panel=} {--F|force}';
}

<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeRelationManagerCommand extends Commands\MakeRelationManagerCommand
{
    protected $hidden = true;

    protected $signature = 'filament:relation-manager {resource} {relationship}';
}

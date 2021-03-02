<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeRelationManagerCommand extends Commands\MakeRelationManagerCommand
{
    protected $signature = 'filament:relation-manager {resource} {relationship}';

    protected $hidden = true;
}

<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeResourceCommand extends Commands\MakeResourceCommand
{
    protected $signature = 'filament:resource {name}';

    protected $hidden = true;
}

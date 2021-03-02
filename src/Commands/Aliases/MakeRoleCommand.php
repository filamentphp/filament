<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeRoleCommand extends Commands\MakeRoleCommand
{
    protected $signature = 'filament:role {name}';

    protected $hidden = true;
}

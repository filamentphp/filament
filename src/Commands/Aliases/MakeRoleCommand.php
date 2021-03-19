<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeRoleCommand extends Commands\MakeRoleCommand
{
    protected $hidden = true;

    protected $signature = 'filament:role {name}';
}

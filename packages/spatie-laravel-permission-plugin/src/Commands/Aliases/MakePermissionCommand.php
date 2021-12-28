<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakePermissionCommand extends Commands\MakePermissionCommand
{
    protected $hidden = true;

    protected $signature = 'filament:resource {name?} {--G|generate} {--P|permissions}';
}

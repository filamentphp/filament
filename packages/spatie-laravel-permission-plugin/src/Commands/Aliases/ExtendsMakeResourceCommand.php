<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class ExtendsMakeResourceCommand extends Commands\ExtendsMakeResourceCommand
{
    protected $hidden = true;

    protected $signature = 'filament:resource {name?} {--G|generate} {--P|permissions : Generate Permissions & Policy for Resource}';
}

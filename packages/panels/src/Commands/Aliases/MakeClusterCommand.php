<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeClusterCommand extends Commands\MakeClusterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:cluster {name?} {--panel=} {--F|force}';
}

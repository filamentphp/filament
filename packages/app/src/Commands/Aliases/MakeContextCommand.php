<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeContextCommand extends Commands\MakeContextCommand
{
    protected $hidden = true;

    protected $signature = 'filament:context {id?} {--F|force}';
}

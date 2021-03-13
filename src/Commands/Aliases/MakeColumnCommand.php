<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeColumnCommand extends Commands\MakeColumnCommand
{
    protected $hidden = true;
    protected $signature = 'filament:column {name} {--R|resource}';
}

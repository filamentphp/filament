<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeFieldCommand extends Commands\MakeFieldCommand
{
    protected $hidden = true;
    protected $signature = 'filament:field {name} {--R|resource}';
}

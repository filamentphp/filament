<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeThemeCommand extends Commands\MakeThemeCommand
{
    protected $hidden = true;

    protected $signature = 'filament:theme {panel?} {--F|force}';
}

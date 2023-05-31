<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class CompileThemeCommand extends Commands\CompileThemeCommand
{
    protected $hidden = true;

    protected $signature = 'filament:theme {panel?} {--W|watch}';
}

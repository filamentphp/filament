<?php

namespace Filament\Commands\Aliases;

class MakeThemeCommand extends \Filament\Commands\MakeThemeCommand
{
    protected $hidden = true;

    protected $signature = 'filament:theme {name}';
}

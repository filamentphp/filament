<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakePanelCommand extends Commands\MakePanelCommand
{
    protected $hidden = true;

    protected $signature = 'filament:panel {id?} {--F|force}';
}

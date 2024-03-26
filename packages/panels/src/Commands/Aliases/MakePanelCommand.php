<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:panel')]
class MakePanelCommand extends Commands\MakePanelCommand
{
    protected $hidden = true;

    protected $signature = 'filament:panel {id?} {--F|force}';
}

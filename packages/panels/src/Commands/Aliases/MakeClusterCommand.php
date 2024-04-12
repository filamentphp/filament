<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:cluster')]
class MakeClusterCommand extends Commands\MakeClusterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:cluster {name?} {--panel=} {--F|force}';
}

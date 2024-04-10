<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:theme')]
class MakeThemeCommand extends Commands\MakeThemeCommand
{
    protected $hidden = true;

    protected $signature = 'filament:theme {panel?} {--pm=} {--F|force}';
}

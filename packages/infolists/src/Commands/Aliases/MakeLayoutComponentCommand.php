<?php

namespace Filament\Infolists\Commands\Aliases;

use Filament\Infolists\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'infolists:layout')]
class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'infolists:layout {name} {--F|force}';
}

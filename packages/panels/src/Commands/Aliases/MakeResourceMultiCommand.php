<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:resource-multi')]
class MakeResourceMultiCommand extends Commands\MakeResourceMultiCommand
{
    protected $hidden = true;

    protected $signature = 'filament:resource-multi';
}

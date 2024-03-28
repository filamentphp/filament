<?php

namespace Filament\Support\Commands\Aliases;

use Filament\Support\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:issue')]
class MakeIssueCommand extends Commands\MakeIssueCommand
{
    protected $hidden = true;

    protected $signature = 'filament:issue';
}

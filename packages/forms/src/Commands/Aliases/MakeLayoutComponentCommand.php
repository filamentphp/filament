<?php

namespace Filament\Forms\Commands\Aliases;

use Filament\Forms\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'forms:layout')]
class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'forms:layout {name} {--F|force}';
}

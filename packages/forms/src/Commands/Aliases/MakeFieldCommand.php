<?php

namespace Filament\Forms\Commands\Aliases;

use Filament\Forms\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'forms:field')]
class MakeFieldCommand extends Commands\MakeFieldCommand
{
    protected $hidden = true;

    protected $signature = 'forms:field {name} {--F|force}';
}

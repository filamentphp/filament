<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:page')]
class MakePageCommand extends Commands\MakePageCommand
{
    protected $hidden = true;

    protected $signature = 'filament:page {name?} {--R|resource=} {--T|type=} {--panel=} {--F|force}';
}

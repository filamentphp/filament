<?php

namespace Filament\Actions\Commands\Aliases;

use Filament\Actions\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:exporter')]
class MakeExporterCommand extends Commands\MakeExporterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:exporter {name?} {--G|generate} {--F|force}';
}

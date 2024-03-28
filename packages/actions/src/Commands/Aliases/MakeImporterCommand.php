<?php

namespace Filament\Actions\Commands\Aliases;

use Filament\Actions\Commands;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'filament:importer')]
class MakeImporterCommand extends Commands\MakeImporterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:importer {name?} {--G|generate} {--F|force}';
}

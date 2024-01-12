<?php

namespace Filament\Actions\Commands\Aliases;

use Filament\Actions\Commands;

class MakeImporterCommand extends Commands\MakeImporterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:importer {name?} {--G|generate} {--F|force}';
}

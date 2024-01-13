<?php

namespace Filament\Actions\Commands\Aliases;

use Filament\Actions\Commands;

class MakeExporterCommand extends Commands\MakeExporterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:exporter {name?} {--G|generate} {--F|force}';
}

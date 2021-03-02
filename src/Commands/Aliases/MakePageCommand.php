<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakePageCommand extends Commands\MakePageCommand
{
    protected $signature = 'filament:page {name} {--R|resource=}';

    protected $hidden = true;
}

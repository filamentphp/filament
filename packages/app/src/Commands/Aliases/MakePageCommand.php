<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakePageCommand extends Commands\MakePageCommand
{
    protected $hidden = true;

    protected $signature = 'filament:page {name?} {--R|resource=} {--T|type=} {--F|force}';
}

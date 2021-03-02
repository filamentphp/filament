<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeUserCommand extends Commands\MakeUserCommand
{
    protected $signature = 'filament:user';

    protected $hidden = true;
}

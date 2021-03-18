<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeUserCommand extends Commands\MakeUserCommand
{
    protected $hidden = true;

    protected $signature = 'filament:user';
}

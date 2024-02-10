<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeIssueCommand extends Commands\MakeIssueCommand
{
    protected $hidden = true;

    protected $signature = 'filament:issue';
}

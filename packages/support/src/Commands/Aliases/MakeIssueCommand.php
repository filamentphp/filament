<?php

namespace Filament\Support\Commands\Aliases;

use Filament\Support\Commands;

class MakeIssueCommand extends Commands\MakeIssueCommand
{
    protected $hidden = true;

    protected $signature = 'filament:issue';
}

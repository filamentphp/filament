<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeTenantRegisterCommand extends Commands\MakeTenantRegisterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:register-tenant {--panel=} {--F|force}';
}

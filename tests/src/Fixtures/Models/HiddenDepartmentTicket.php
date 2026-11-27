<?php

namespace Filament\Tests\Fixtures\Models;

class HiddenDepartmentTicket extends DepartmentTicket
{
    protected $hidden = ['quantity', 'created_at'];
}

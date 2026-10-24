<?php

namespace Filament\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartmentTicket extends Pivot
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'department_id' => 'integer',
            'ticket_id' => 'integer',
            'quantity' => 'integer',
            'price' => 'integer',
            'sort' => 'integer',
        ];
    }
}

<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\TicketMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory()
    {
        return TicketMessageFactory::new();
    }
}

<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\TicketMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class)->withTrashed();
    }

    protected static function newFactory()
    {
        return TicketMessageFactory::new();
    }
}

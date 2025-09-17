<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory()
    {
        return TicketFactory::new();
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class);
    }
}

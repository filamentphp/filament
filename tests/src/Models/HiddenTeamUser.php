<?php

namespace Filament\Tests\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HiddenTeamUser extends Pivot
{
    protected $hidden = ['created_at', 'user_id'];

    protected $casts = ['role' => 'string'];
}

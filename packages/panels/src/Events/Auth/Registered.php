<?php

namespace Filament\Events\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

class Registered
{
    use SerializesModels;

    public function __construct(
        readonly public Authenticatable $user,
    ) {}
}

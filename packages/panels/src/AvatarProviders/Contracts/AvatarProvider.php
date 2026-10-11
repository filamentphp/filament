<?php

namespace Filament\AvatarProviders\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

interface AvatarProvider
{
    public function get(Model | Authenticatable $record): string;
}

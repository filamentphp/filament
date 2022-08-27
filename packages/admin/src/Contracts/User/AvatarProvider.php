<?php

declare(strict_types=1);

namespace Filament\Contracts\User;

use Illuminate\Database\Eloquent\Model;

interface AvatarProvider
{
    /**
     * Fetch the avatar for the given user.
     *
     * @param Model $user
     * @return string
     */
    public function get(Model $user): string;
}

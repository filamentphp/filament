<?php

namespace Filament\Auth\MultiFactor\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface HasBeforeChallengeHook
{
    public function beforeChallenge(Authenticatable $user): void;
}

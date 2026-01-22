<?php

namespace App\Policies;

use App\Models\ProgramStatusHistory;
use App\Models\User;

class ProgramStatusHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBpk();
    }

    public function view(User $user, ProgramStatusHistory $history): bool
    {
        return $user->isBpk();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ProgramStatusHistory $history): bool
    {
        return false;
    }

    public function delete(User $user, ProgramStatusHistory $history): bool
    {
        return false;
    }
}

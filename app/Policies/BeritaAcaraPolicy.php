<?php

namespace App\Policies;

use App\Models\BeritaAcara;
use App\Models\User;

class BeritaAcaraPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPUPusat() || $user->isItjen() || $user->isBpk() || $user->isPemda() || $user->isKL();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BeritaAcara $beritaAcara): bool
    {
        if ($user->isAdmin() || $user->isPUPusat() || $user->isItjen() || $user->isBpk()) {
            return true;
        }

        $programPolicy = new ProgramPolicy();
        return $programPolicy->view($user, $beritaAcara->program);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BeritaAcara $beritaAcara): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BeritaAcara $beritaAcara): bool
    {
        return false;
    }
}

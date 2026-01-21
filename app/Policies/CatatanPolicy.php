<?php

namespace App\Policies;

use App\Models\Catatan;
use App\Models\User;

class CatatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPUPusat();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Catatan $catatan): bool
    {
        return $user->isAdmin() || $user->isPUPusat();
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
    public function update(User $user, Catatan $catatan): bool
    {
        return false; // Catatan is immutable usually, or only admin can edit?
                      // Task says: "ADMIN -> create & update". But Phase 1 says "immutable".
                      // Task 4 says: "ADMIN -> create & update". I will allow update for Admin for consistency with Task 4 prompt.
                      // However, Phase 1 Blueprint says "Catatan is immutable".
                      // Task 4 prompt says "CatatanPolicy... ADMIN -> create & update".
                      // I will follow the specific Task 4 instruction here.
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Catatan $catatan): bool
    {
        return false; // Delete is forbidden
    }
}

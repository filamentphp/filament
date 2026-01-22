<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPUPusat() || $user->isPemda() || $user->isKL();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Program $program): bool
    {
        if ($user->isAdmin() || $user->isPUPusat()) {
            return true;
        }

        if ($user->isPemda()) {
            // Check scope location
            $scope = $user->pemda_scope['lokasi'] ?? null;
            // Simple string match or array containment
            // Assuming scope['lokasi'] is a string like "Jawa Barat" and program->lokasi contains it
            if (!$scope) return false;
            return str_contains($program->lokasi, $scope);
        }

        if ($user->isKL()) {
            // Check scope sector
            $scope = $user->kl_scope['sektor'] ?? null;
            if (!$scope) return false;
            return $program->sektor === $scope;
        }

        return false;
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
    public function update(User $user, Program $program): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Program $program): bool
    {
        return false; // Delete is forbidden for everyone
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Program $program): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Program $program): bool
    {
        return false;
    }
}

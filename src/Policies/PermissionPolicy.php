<?php

namespace Filament\Policies;

use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine if the authenticated user view users.
     *
     * @param  User  $authenticated
     * @param  User  $user
     * @return bool
     */
    public function view($authenticated): Response
    {
        return $authenticated->can('view permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to view permissions.'));
    }

    /**
     * Determine if the authenticated user can update a user.
     *
     * @param  User  $authenticated
     * @return bool
     */
    public function edit($authenticated): Response
    {
        return $authenticated->can('edit permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to edit permissions.'));
    }
}
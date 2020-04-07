<?php

namespace Filament\Policies;

use Illuminate\Auth\Access\Response;

class RolePolicy 
{
    /**
     * Determine if the authenticated user can view roles.
     *
     * @param  User  $authenticated
     * @return bool
     */
    public function view($authenticated): Response
    {
        return $authenticated->can('view roles')
                ? Response::allow()
                : Response::deny(__('You are not allowed to view roles.'));
    }

    /**
     * Determine if the authenticated user can edit a role.
     *
     * @param  User  $authenticated
     * @return bool
     */
    public function edit($authenticated): Response
    {
        return $authenticated->can('edit roles')
                ? Response::allow()
                : Response::deny(__('You are not allowed to edit roles.'));
    }
}
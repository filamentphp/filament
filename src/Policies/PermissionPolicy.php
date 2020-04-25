<?php

namespace Filament\Policies;

use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine if the authenticated user can view permissions.
     *
     * @param  User  $authenticated
     * @return bool
     */
    public function view($authenticated): Response
    {
        return $authenticated->can('view permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to view permissions.'));
    }

    /**
     * Determine if the authenticated user can edit a permission.
     *
     * @param  User  $authenticated
     * @param  Permission  $permission
     * @return bool
     */
    public function edit($authenticated, $permission): Response
    {
        if ($permission->is_system) {
            return Response::deny(__('You are not allowed to edit a system permission.'));
        }

        return $authenticated->can('edit permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to edit permissions.'));
    }

    /**
     * Determine if the authenticated user can create a permission.
     *
     * @param  User  $authenticated
     * @return bool
     */
    public function create($authenticated): Response
    {
        return $authenticated->can('create permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to create a permission.'));
    }

    /**
     * Determine if the authenticated user can delete a permission.
     *
     * @param  User  $authenticated
     * @param  Permission  $permission
     * @return bool
     */
    public function delete($authenticated, $permission): Response
    {
        return !$permission->is_system && $authenticated->can('delete permissions')
                ? Response::allow()
                : Response::deny(__('You are not allowed to delete permissions.'));
    }
}
<?php

namespace Filament\Policies;

use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the authenticated user can view users.
     *
     * @param  User  $authenticated
     * @param  User  $user
     * @return bool
     */
    public function view($authenticated): Response
    {
        return $authenticated->can('view users')
                ? Response::allow()
                : Response::deny(__('You are not allowed to view users.'));
    }

    /**
     * Determine if the authenticated user can edit a user.
     *
     * @param  User  $authenticated
     * @param  User  $user
     * @return bool
     */
    public function edit($authenticated, $user): Response
    {
        if ($authenticated->can('edit users')) {
            return Response::allow();
        }

        return $authenticated->id === $user->id
                ? Response::allow()
                : Response::deny(__('You are not allowed to edit other users.'));
    }

    /**
     * Determine if the authenticated user can create users.
     *
     * @param  User  $authenticated
     * @param  User  $user
     * @return bool
     */
    public function create($authenticated): Response
    {
        return $authenticated->can('create users')
                ? Response::allow()
                : Response::deny(__('You are not allowed to create users.'));
    }

    /**
     * Determine if the authenticated user can delete a user.
     *
     * @param  User  $authenticated
     * @param  User  $user
     * @return bool
     */
    public function delete($authenticated, $user): Response
    {
        if ($authenticated->can('delete users')) {
            return Response::allow();
        }

        return $authenticated->id === $user->id
                ? Response::allow()
                : Response::deny(__('You are not allowed to delete other users.'));
    }
}
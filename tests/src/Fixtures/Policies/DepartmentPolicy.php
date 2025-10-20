<?php

namespace Filament\Tests\Fixtures\Policies;

use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function viewAny(User $user): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::viewAny', fn (): bool => true);

        return app(DepartmentPolicy::class . '::viewAny');
    }

    public function view(User $user, Department $department): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::view', fn (): bool => true);

        return app(DepartmentPolicy::class . '::view');
    }

    public function create(User $user): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::create', fn (): bool => true);

        return app(DepartmentPolicy::class . '::create');
    }

    public function update(User $user, Department $department): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::update', fn (): bool => true);

        return app(DepartmentPolicy::class . '::update');
    }

    public function delete(User $user, Department $department): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::delete', fn (): bool => true);

        return app(DepartmentPolicy::class . '::delete');
    }

    public function deleteAny(User $user): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::deleteAny', fn (): bool => true);

        return app(DepartmentPolicy::class . '::deleteAny');
    }

    public function restore(User $user, Department $department): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::restore', fn (): bool => true);

        return app(DepartmentPolicy::class . '::restore');
    }

    public function restoreAny(User $user): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::restoreAny', fn (): bool => true);

        return app(DepartmentPolicy::class . '::restoreAny');
    }

    public function forceDelete(User $user, Department $department): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::forceDelete', fn (): bool => true);

        return app(DepartmentPolicy::class . '::forceDelete');
    }

    public function forceDeleteAny(User $user): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::forceDeleteAny', fn (): bool => true);

        return app(DepartmentPolicy::class . '::forceDeleteAny');
    }

    public function replicate(User $user, Department $ticket): bool | Response
    {
        app()->bindIf(DepartmentPolicy::class . '::replicate', fn (): bool => true);

        return app(DepartmentPolicy::class . '::replicate');
    }
}

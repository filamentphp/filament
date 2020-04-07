<?php

namespace Filament\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Contracts\Role as RoleContract;

class Roles extends Component
{
    use AuthorizesRequests, WithPagination;

    public function render()
    {
        $roleClass = app(RoleContract::class);
        $this->authorize('view', $roleClass);

        $roles = $roleClass::paginate(48);

        return view('filament::livewire.roles', [
            'roles' => $roles,
            'headers' => [
                'id' => __('ID'),
                'name' => __('Name'), 
                'description' => __('Description'),
            ],
            'rows' => $roles->map(function ($role) {
                /*
                $role->row_action = [
                    'url' => route('filament.admin.roles.edit', ['id' => $role->id]),
                    'label' => __('Edit'),
                ];
                */
                return $role;
            })->values()->toArray(),
        ]);
    }
}
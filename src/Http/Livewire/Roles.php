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

        return view('filament::livewire.roles', [
            'roles' => $roleClass::paginate(24),
        ]);
    }
}
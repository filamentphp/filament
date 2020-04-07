<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Contracts\Role as RoleContract;

class Roles extends Component
{
    use WithPagination;

    public function render()
    {
        $roleClass = app(RoleContract::class);

        return view('filament::livewire.roles', [
            'roles' => $roleClass::paginate(24),
        ]);
    }
}
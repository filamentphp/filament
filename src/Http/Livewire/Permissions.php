<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\WithDataTable;
use Filament\Models\Permission;

class Permissions extends Component
{
    use AuthorizesRequests, WithDataTable;

    public function render()
    {
        $this->authorize('view', Permission::class);

        $permissions = Permission::search($this->search)
                            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                            ->paginate($this->perPage);

        return view('filament::livewire.permissions.index', [
            'title' => __('filament::admin.permissions'),
            'permissions' => $permissions,
        ]);
    }
}
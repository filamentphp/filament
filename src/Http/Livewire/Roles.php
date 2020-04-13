<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\WithDataTable;
use Filament\Models\Role;
use Illuminate\Support\HtmlString;

class Roles extends Component
{
    use AuthorizesRequests, WithDataTable;

    public function render()
    {
        $this->authorize('view', Role::class);

        $roles = Role::orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('filament::livewire.roles', [
            'title' => __('filament::admin.roles'),
            'roles' => $roles,
        ]);
    }
}
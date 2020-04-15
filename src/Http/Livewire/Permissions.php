<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\WithDataTable;
use Filament\Models\Permission;
use Illuminate\Support\HtmlString;

class Permissions extends Component
{
    use AuthorizesRequests, WithDataTable;

    protected $listeners = ['filament.permissionUpdated' => 'showPermissionUpdatedNotification'];

    public function showPermissionUpdatedNotification($permission)
    {
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $permission['name']]),
        ]);

        $this->render();
    }

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
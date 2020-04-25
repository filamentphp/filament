<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Models\Permission;

class PermissionDelete extends Component
{
    use AuthorizesRequests;
    
    public $permission;

    public function mount(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $this->permission = $permission;
    }

    public function delete()
    {
        $permission = $this->permission;
        $this->permission->delete();

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.deleted', ['item' => $permission->name]),
        ]);

        return redirect()->route('filament.admin.permissions.index');
    }

    public function render()
    {        
        return view('filament::livewire.confirm-delete', [
            'title' => __('filament::permissions.delete'),
            'item' => $this->permission->name,
        ]);
    }
}
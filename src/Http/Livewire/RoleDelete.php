<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Models\Role;

class RoleDelete extends Component
{
    use AuthorizesRequests;
    
    public $role;

    public function mount(Role $role)
    {
        $this->authorize('delete', $role);

        $this->role = $role;
    }

    public function delete()
    {
        $role = $this->role;
        $this->role->delete();

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.deleted', ['item' => $role->name]),
        ]);

        return redirect()->route('filament.admin.roles.index');
    }

    public function render()
    {        
        return view('filament::livewire.confirm-delete', [
            'title' => __('filament::roles.delete'),
            'item' => $this->role->name,
        ]);
    }
}
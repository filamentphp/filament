<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Filament\Models\Role;

class RoleDelete extends Component
{
    public $role;

    public function mount(Role $role)
    {
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
            'title' => __('filament::permissions.roles.delete'),
            'message' => __('filament::permissions.roles.delete_confirm', ['role' => $this->role->name]),
        ]);
    }
}
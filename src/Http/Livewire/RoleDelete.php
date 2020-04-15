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
        $this->emit('filament.roleDeleted', $this->role);
        $this->emit('filament.toggleModal', 'delete-role-'.$this->role->id);
        $this->role->delete();
    }

    public function render()
    {        
        return view('filament::livewire.confirm-delete', [
            'title' => __('filament::permissions.roles.delete'),
            'message' => __('filament::permissions.roles.delete_confirm', ['role' => $this->role->name]),
        ]);
    }
}
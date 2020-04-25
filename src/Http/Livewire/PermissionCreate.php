<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Models\Permission;

class PermissionCreate extends FormComponent
{    
    public function mount()
    {        
        $this->authorize('create', Permission::class);

        $this->setFieldset();
        $this->setFormProperties();
    }

    public function success()
    {
        $input = collect($this->form_data);

        $permission = Permission::create($input->all());      
        
        if (auth()->user()->can('edit roles')) {
            $permission->syncRoles($input->get('roles'));
        }

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.created', ['item' => $permission->name]),
        ]);

        return redirect()->route('filament.admin.permissions.index');
    }

    public function render()
    {        
        return view('filament::livewire.permissions.create-edit', [
            'title' => __('filament::permissions.create'),
            'fields' => $this->fields(),
        ]);
    }
}

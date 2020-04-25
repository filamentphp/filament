<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Models\Permission;

class PermissionEdit extends FormComponent
{    
    public function mount(Permission $permission)
    {        
        $this->model = $permission;
        $this->authorize('edit', $this->model);

        $this->setFieldset();
        $this->setFormProperties();
    }

    public function save()
    {
        $this->authorize('edit', $this->model);
        $this->submit();
    }

    public function success()
    {
        $input = collect($this->form_data);

        $this->model->update($input->all());     
        
        if (auth()->user()->can('edit roles')) {
            $this->model->syncRoles($input->get('roles'));
        }

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->model->name]),
        ]);

        return redirect()->route('filament.admin.permissions.index');
    }

    public function render()
    {        
        return view('filament::livewire.permissions.create-edit', [
            'title' => __('filament::permissions.edit'),
            'fields' => $this->fields(),
        ]);
    }
}

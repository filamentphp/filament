<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Models\Role;

class RoleEdit extends FormComponent
{    
    public function mount(Role $role)
    {        
        $this->model = $role;
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
        $this->model->syncPermissions($input->get('permissions'));

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->model->name]),
        ]);

        return redirect()->route('filament.admin.roles.index');
    }

    public function render()
    {        
        return view('filament::livewire.roles.create-edit', [
            'title' => __('filament::roles.edit'),
            'fields' => $this->fields(),
        ]);
    }
}

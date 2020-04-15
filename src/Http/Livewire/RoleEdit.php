<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Models\Role;

class RoleEdit extends FormComponent
{    
    public function mount(Role $role)
    {        
        $this->model = $role;
        $this->authorize('view', $this->model);

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

        $this->emit('filament.roleUpdated', $this->model);
        $this->emit('filament.toggleModal', 'edit-role-'.$this->model->id);
    }

    public function render()
    {        
        return view('filament::livewire.roles.edit', [
            'title' => __('filament::permissions.roles.edit'),
            'fields' => $this->fields(),
        ]);
    }
}

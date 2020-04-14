<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Models\Role;

class RoleEdit extends FormComponent
{    
    public function mount($id)
    {        
        $this->model = Role::findOrFail($id);
        $this->authorize('edit', $this->model);

        $this->setFieldset();
        $this->setFormProperties();
    }

    public function success()
    {
        $this->model->update($this->form_data);

        $this->emit('filament.roleUpdated', $this->model);
        $this->emit('filament.toggleModal', $this->model->id);
    }

    public function render()
    {        
        return view('filament::livewire.roles.edit', [
            'fields' => $this->fields(),
            'role' => $this->model,
        ]);
    }
}

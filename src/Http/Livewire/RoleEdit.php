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
        $input = collect($this->form_data);
        $this->model->update($input->all());

        $this->emit('filament.toggleModal', $this->model->id);

        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->model->name]),
        ]);
    }

    public function render()
    {        
        return view('filament::livewire.roles.edit', [
            'fields' => $this->fields(),
            'role' => $this->model,
        ]);
    }
}

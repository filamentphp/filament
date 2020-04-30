<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\Livewire\HasForm;
use Filament\Models\Role;

class RoleEdit extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount(Role $role)
    {        
        $this->authorize('edit', $role);
        $this->setupForm($role);
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
        
        if (auth()->user()->can('edit permissions')) {
            $this->model->syncPermissions($input->get('permissions'));
        }

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->model->name]),
        ]);

        return redirect()->route('filament.admin.roles.index');
    }

    public function render()
    {        
        $fields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });
        
        return view('filament::livewire.roles.create-edit', [
            'title' => __('filament::roles.edit'),
            'fields' => $fields,
        ]);
    }
}

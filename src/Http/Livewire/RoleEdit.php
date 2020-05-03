<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\HasForm;
use Filament\Models\Role;

class RoleEdit extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount(Role $role)
    {        
        $this->authorize('edit', $role);
        $this->initForm($role);
    }

    public function save()
    {
        $this->authorize('edit', $this->model);
        $this->submit();
    }

    public function success()
    {
        $input = collect($this->model_data);

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
        $groupedFields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });
        
        return view('filament::livewire.tabbed-form', [
            'title' => __('filament::roles.edit'),
            'groupedFields' => $groupedFields,
        ]);
    }
}

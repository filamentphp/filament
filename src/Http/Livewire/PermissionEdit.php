<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\Livewire\HasForm;
use Filament\Models\Permission;

class PermissionEdit extends Component
{    
    use AuthorizesRequests, HasForm;
    
    public function mount(Permission $permission)
    {        
        $this->authorize('edit', $permission);
        $this->setupForm($permission);
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
        $fields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });

        return view('filament::livewire.permissions.create-edit', [
            'title' => __('filament::permissions.edit'),
            'fields' => $fields,
        ]);
    }
}

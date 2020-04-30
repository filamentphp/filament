<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\Livewire\HasForm;
use Filament\Models\Permission;

class PermissionCreate extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount()
    {        
        $this->authorize('create', Permission::class);
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
        $fields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });

        return view('filament::livewire.permissions.create-edit', [
            'title' => __('filament::permissions.create'),
            'fields' => $fields,
        ]);
    }
}

<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\HasForm;
use Filament\Models\Permission;

class PermissionCreate extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount()
    {        
        $this->authorize('create', Permission::class);
        $this->initForm();
    }

    public function success()
    {
        $model_input = collect($this->model_data);

        $permission = Permission::create($model_input->all());      
        $permission->syncMeta($this->model_meta);

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
        $groupedFields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });

        return view('filament::livewire.tabbed-form', [
            'title' => __('filament::permissions.create'),
            'groupedFields' => $groupedFields,
        ]);
    }
}

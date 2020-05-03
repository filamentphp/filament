<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\HasForm;
use Filament\Models\Role;

class RoleCreate extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount()
    {        
        $this->authorize('create', Role::class);
    }

    public function success()
    {
        $input = collect($this->model_data);

        $role = Role::create($input->all());     
        
        if (auth()->user()->can('edit permissions')) {
            $role->syncPermissions($input->get('permissions'));
        }

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.created', ['item' => $role->name]),
        ]);

        return redirect()->route('filament.admin.roles.index');
    }

    public function render()
    {        
        $groupedFields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });

        return view('filament::livewire.tabbed-form', [
            'title' => __('filament::roles.create'),
            'groupedFields' => $groupedFields,
        ]);
    }
}

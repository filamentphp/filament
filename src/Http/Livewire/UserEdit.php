<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Contracts\User as UserContract;

class UserEdit extends FormComponent
{    
    public function mount($id)
    {        
        $userClass = app(UserContract::class);  
        $this->model = $userClass::findOrFail($id);
        $this->authorize('edit', $this->model);

        $this->setFieldset();
        $this->setFormProperties();
    }

    public function success()
    {
        $input = collect($this->form_data);

        if (!auth()->user()->is_super_admin) {
            $input->forget('is_super_admin');
        }

        if (is_null($input->get('password'))) {
            $input->forget('password');
        }

        $this->model->update($input->all());

        if (auth()->user()->can('edit user roles')) {
            $this->model->syncRoles($input->get('roles'));
        }

        if (auth()->user()->can('edit user permissions')) {
            $this->model->syncPermissions($input->get('direct_permissions'));
        }

        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->model->name]),
        ]);

        $this->emit('filament.userUpdated', $this->model->id);
    }

    public function render()
    {        
        return view('filament::livewire.users.edit', [
            'title' => __('filament::users.account', ['name' => $this->model->name]),
            'fields' => $this->fields(),
            'user' => $this->model,
        ]);
    }
}

<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;

class UserEdit extends FormComponent
{    
    public function fields()
    {
        return $this->getFieldset(__FILE__)::fields($this->model);
    }

    public function rulesIgnoreRealtime()
    {
        return $this->getFieldset(__FILE__)::rulesIgnoreRealtime();
    }

    public function success()
    {
        $input = collect($this->form_data);

        if (is_null($input->get('password'))) {
            $input->forget('password');
        }
        
        $this->model->update($input->all());
        $this->model->syncRoles($input->get('roles'));

        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::actions.updated', ['item' => $this->model->name]),
        ]);

        $this->emit('filament.userUpdated', $this->model->id);

        $this->updateAuthUser();
    }

    public function saveField($field_name)
    {
        $this->model->$field_name = $this->form_data[$field_name];
        $this->model->save();
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::actions.updated', ['item' => $field_name]),
        ]);

        $this->updateAuthUser();
    }

    protected function updateAuthUser()
    {
        if (auth()->user()->id === $this->model->id) {
            $this->emit('filament.userUpdated');
        }
    }

    public function render()
    {        
        return view('filament::livewire.user-edit', [
            'fields' => $this->fields(),
            'user' => $this->model,
        ]);
    }

    public function saveAndGoBackResponse()
    {
        return redirect()->route('filament.admin.users.index');
    }
}

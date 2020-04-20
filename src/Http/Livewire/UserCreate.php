<?php

namespace Filament\Http\Livewire;

use Filament\Support\Livewire\FormComponent;
use Filament\Contracts\User as UserContract;

class UserCreate extends FormComponent
{    
    public function mount()
    {        
        $this->authorize('create', app(UserContract::class));

        $this->setFieldset();
        $this->setFormProperties();
    }

    public function success()
    {
        $input = collect($this->form_data);

        $user = app(UserContract::class)::create($input->all());        

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.created', ['item' => $user->name]),
        ]);

        return redirect()->route('filament.admin.users.index');
    }

    public function render()
    {        
        return view('filament::livewire.users.create-edit', [
            'title' => __('filament::users.create'),
            'fields' => $this->fields(),
        ]);
    }
}

<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\Livewire\HasForm;
use Filament\Traits\Livewire\HasFileUploads;

class UserEdit extends Component
{    
    use AuthorizesRequests, HasForm, HasFileUploads;

    protected $listeners = [
        'filament.fileUploadError' => 'fileUploadError',
        'filament.fileUpdate' => 'fileUpdate',
    ];

    public function mount($user)
    {        
        $this->authorize('edit', $user);
        $this->model = $user;

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

        $this->emitUp('userUpdated', $this->model->id);
    }

    public function render()
    {        
        return view('filament::livewire.users.create-edit', ['fields' => $this->fields()]);
    }
}

<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\Livewire\HasForm;
use Filament\Fields\File\Traits\HasFileUploads;

class UserEdit extends Component
{    
    use AuthorizesRequests, HasForm, HasFileUploads;

    protected $listeners = [
        'filament.fileUploadError' => 'fileUploadError',
        'filament.saveFileField' => 'saveFileField',
    ];

    public function mount($user)
    {        
        $this->authorize('edit', $user);
        $this->setupForm($user);
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
        $groupedFields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });
        
        return view('filament::livewire.tabbed-form', compact('groupedFields'));
    }
}

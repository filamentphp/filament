<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\HasForm;
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
        $this->initForm($user);
    }

    public function success()
    {
        $model_input = collect($this->model_data);

        if (!auth()->user()->is_super_admin) {
            $model_input->forget('is_super_admin');
        }

        if (is_null($model_input->get('password'))) {
            $model_input->forget('password');
        }

        $this->model->update($model_input->all());
        $this->model->syncMeta($this->model_meta);

        if (auth()->user()->can('edit user roles')) {
            $this->model->syncRoles($model_input->get('roles'));
        }

        if (auth()->user()->can('edit user permissions')) {
            $this->model->syncPermissions($model_input->get('direct_permissions'));
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

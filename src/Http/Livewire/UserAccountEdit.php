<?php

namespace Filament\Http\Livewire;

use Illuminate\Validation\Rule;
use Filament\Support\Livewire\FormComponent;
use Filament\Support\Fields\ArrayField;
use Filament\Support\Fields\Field;
use Spatie\Permission\Contracts\Role as RoleContract;

class UserAccountEdit extends FormComponent
{
    public function fields()
    {
        return [
            Field::make('Name')->input()->rules(['required', 'string', 'max:255']),
            Field::make('Email')->input('email')->rules([
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($this->model->id),
            ]),
            Field::make('Password')
                ->input('password')
                ->rules(['sometimes', 'confirmed'])
                ->help('Leave blank to keep current password.'),
            Field::make('Confirm Password', 'password_confirmation')
                ->input('password'),
        ];
    }

    public function success()
    {
        $input = collect($this->form_data);

        // if password is left blank then remove from input to update
        if (!$input->get('password')) {
            $input->forget('password');
        }
        
        $this->model->update($input->all());

        $this->emit('notification.notify', [
            'type' => 'success',
            'message' => __('filament::user.updated', ['name' => $this->model->name]),
        ]);

        $this->emit('userUpdated', $this->model->id);

        if (auth()->user()->id === $this->model->id) {
            $this->emit('authUserUpdated');
        }
    }

    public function saveAndGoBackResponse()
    {
        return redirect()->route('filament.admin.users.index');
    }

    public function getRolesProperty()
    {
        $roleClass = app(RoleContract::class);
        return $roleClass::all();
    }
}

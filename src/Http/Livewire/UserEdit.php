<?php

namespace Filament\Http\Livewire;

use Illuminate\Validation\Rule;
use Filament\Support\Livewire\FormComponent;
use Filament\Support\Fields\Field;
use Spatie\Permission\Contracts\Role as RoleContract;

class UserEdit extends FormComponent
{
    public function fields()
    {
        return [
            Field::make('Name')
                ->input()
                ->rules(['required', 'string', 'max:255'])
                ->group('account'),
            Field::make('Avatar')
                ->rules('array')
                ->file()
                ->multiple()
                ->fileRules('image')
                ->fileValidationMessages([
                    'image' => __('The Avatar must be a valid image.'),
                ])
                ->group('account'),
            Field::make('Email')
                ->input('email')
                ->rules([
                    'required', 
                    'string', 
                    'email', 
                    'max:255', 
                    Rule::unique('users', 'email')->ignore($this->model->id),
                ])
                ->group('account'),
            Field::make('Password')
                ->input('password')
                ->autocomplete('new-password')
                ->rules(['sometimes', 'confirmed'])
                ->help('Leave blank to keep current password.')
                ->group('account'),
            Field::make('Confirm Password', 'password_confirmation')
                ->input('password')
                ->autocomplete('new-password')
                ->group('account'),
            Field::make('filament::permissions.super_admin', 'is_super_admin')
                ->checkbox()
                ->help(__('filament::permissions.super_admin_info'))
                ->group('permissions'),
            Field::make('filament::permissions.roles', 'roles')
                ->checkboxes($this->roleIds)
                ->default($this->userRoleIds)
                ->rules([Rule::exists('roles', 'id')])
                ->group('permissions'),
        ];
    }

    public function rulesIgnoreRealtime()
    {
        return ['confirmed'];
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

        if (auth()->user()->id === $this->model->id) {
            $this->emit('filament.authUserUpdated');
        }
    }

    public function saveField($field_name)
    {
        $this->model->$field_name = $this->form_data[$field_name];
        $this->model->save();
        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::actions.updated', ['item' => $field_name]),
        ]);
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

    public function getRoleIdsProperty()
    {
        $roleClass = app(RoleContract::class);
        return $roleClass::orderBy('name')->pluck('id', 'name')->all();
    }

    public function getUserRoleIdsProperty()
    {
        return array_map('strval', $this->model->roles->pluck('id')->all());
    }
}

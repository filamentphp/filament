<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Spatie\Permission\Contracts\Role as RoleContract;

class UserEdit extends Component
{
    public $user;
    public $name;
    public $email;
    public $is_super_admin;
    public $user_roles;

    public function mount($user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_super_admin = $user->is_super_admin;
        $this->user_roles = $user->roles;
    }

    public function update()
    {
        $this->emit('notification.close');

        $validatedData = $this->validate([
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'is_super_admin' => 'boolean',
            'user_roles.*' => 'exists:roles,id',
        ]);

        $this->user->update($validatedData);

        if (isset($validatedData['user_roles'])) {
            $this->user->syncRoles($validatedData['user_roles']);
        }

        $this->emit('userUpdated', $this->user->id);

        if (auth()->user()->id === $this->user->id) {
            $this->emit('authUserUpdated');
        }

        $this->emit('notification.notify', [
            'type' => 'success',
            'message' => __('filament::user.updated', ['name' => $this->name]),
        ]);
    }

    public function render()
    {
        $roleClass = app(RoleContract::class);

        return view('filament::livewire.user-edit', [
            'roles' => $roleClass::all(),
        ]);
    }
}
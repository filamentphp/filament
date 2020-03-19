<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Spatie\Permission\Contracts\Role as RoleContract;

class UserEditForm extends Component
{
    public $user;
    public $name;
    public $email;

    public function mount($user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
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
        ]);

        $this->user->update($validatedData);

        $this->emit('notification.notify', [
            'type' => 'success',
            'message' => "User {$this->name} updated successfully.",
        ]);

        if (auth()->user()->id === $this->user->id) {
            $this->emit('userUpdated', $this->user);
        }
    }

    public function render()
    {
        $roleClass = app(RoleContract::class);

        return view('filament::livewire.user-edit-form', [
            'roles' => $roleClass::all(),
        ]);
    }
}
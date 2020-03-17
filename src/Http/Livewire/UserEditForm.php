<?php

namespace Alpine\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;

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
        $validatedData = $this->validate([
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($this->user->id),
            ],
        ]);

        $this->user->update($validatedData);

        $this->emit('userUpdated');

        session()->flash('alert', [
            'type' => 'success',
            'message' => "User {$this->name} updated successfully.",
        ]);
    }

    public function render()
    {
        return view('alpine::livewire.user-edit-form');
    }
}
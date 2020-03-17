<?php

namespace Alpine\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class UserEditForm extends Component
{
    use AuthorizesRequests;

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
        $this->authorize('update', $this->user);

        $validatedData = $this->validate([
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($this->user->id),
            ],
        ]);

        $this->user->update($validatedData);

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
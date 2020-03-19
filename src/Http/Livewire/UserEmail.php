<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class UserEmail extends Component
{
    public $email;
    public $classes;

    protected $listeners = ['userUpdated' => 'updateEmail'];

    public function mount(string $email, string $classes = '')
    {
        $this->email = $email;
        $this->classes = $classes;
    }

    public function updateEmail(array $user) {
        $this->email = collect($user)->get('email', $this->email);
    }

    public function render()
    {
        return view('filament::livewire.user-email');
    }
}
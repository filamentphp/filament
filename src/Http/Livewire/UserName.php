<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class UserName extends Component
{
    public $name;
    public $classes;

    protected $listeners = ['userUpdated' => 'updateName'];

    public function mount(string $name, string $classes = '')
    {
        $this->name = $name;
        $this->classes = $classes;
    }

    public function updateName(array $user) {
        $this->name = collect($user)->get('name', $this->name);
    }

    public function render()
    {
        return view('filament::livewire.user-name');
    }
}
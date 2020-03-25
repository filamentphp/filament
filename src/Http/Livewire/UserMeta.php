<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class UserMeta extends Component
{
    public $user;

    protected $listeners = ['userUpdated' => 'render'];

    public function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('filament::livewire.user-meta');
    }
}
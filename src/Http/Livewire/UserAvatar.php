<?php

namespace Alpine\Http\Livewire;

use Livewire\Component;

class UserAvatar extends Component
{
    public $user;
    public $size;
    public $classes;

    protected $listeners = ['userUpdated' => 'render'];

    public function mount($user, int $size, string $classes = '')
    {
        $this->user = $user;
        $this->size = $size;
        $this->classes = $classes;
    }

    public function render()
    {
        return view('alpine::livewire.user-avatar');
    }
}
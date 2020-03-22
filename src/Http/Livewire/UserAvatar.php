<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Filament\Contracts\User as UserContract;

class UserAvatar extends Component
{
    public $user;
    public $size;
    public $classes;

    protected $listeners = ['userUpdated' => 'render'];

    public function mount(int $userId, int $size, string $classes = '')
    {
        $this->user = $this->getUser($userId);
        $this->size = $size;
        $this->classes = $classes;
    }

    public function updateUser(int $id)
    {
        if ($this->user->id === $id) {
            $this->user = $this->getUser($id);
        }
    }

    public function render()
    {
        return view('filament::livewire.user-avatar');
    }

    protected function getUser(int $userId)
    {
        $userClass = app(UserContract::class);
        
        return $userClass::findOrFail($userId);
    }
}
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

    public function render()
    {
        return <<<'blade'
            <img @if ($classes) class="{{ $classes }}" @endif 
                src="{{ $user->avatar($size) }}" 
                srcset="{{ $user->avatar($size * 2) }} 2x, {{ $user->avatar($size * 3) }} 3x" 
                alt="{{ $user->name }}" 
                width="{{ $size }}" 
                height="{{ $size }}"
                load="lazy">
        blade;
    }

    protected function getUser(int $userId)
    {
        $userClass = app(UserContract::class);
        
        return $userClass::findOrFail($userId);
    }
}
<?php

namespace Filament\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Filament\Contracts\User as UserContract;

class User extends Component
{
    use AuthorizesRequests;

    public $user;

    protected $listeners = ['userUpdated'];

    public function mount($id)
    {
        $this->user = $this->getUser($id);
        $this->authorize('edit', $this->user);
    }

    public function userUpdated(int $userId)
    {
        $this->user = $this->getUser($userId);

        $this->emit('filament.notification.notify', [
            'type' => 'success',
            'message' => __('filament::notifications.updated', ['item' => $this->user->name]),
        ]);
    }

    public function render()
    {        
        return view('filament::livewire.users.user', [
            'title' => __('filament::users.account', ['name' => $this->user->name]),
            'user' => $this->user,
        ]);
    }

    protected function getUser(int $userId)
    {
        $userClass = app(UserContract::class);
        
        return $userClass::findOrFail($userId);
    }
}
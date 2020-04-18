<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserDelete extends Component
{
    use AuthorizesRequests;
    
    public $user;

    public function mount($user)
    {
        $this->authorize('delete', $user);

        $this->user = $user;
    }

    public function delete()
    {
        $user = $this->user;
        $this->user->delete();

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.deleted', ['item' => $user->name]),
        ]);

        return redirect()->route('filament.admin.users.index');
    }

    public function render()
    {        
        return view('filament::livewire.confirm-delete', [
            'title' => __('filament::users.delete'),
            'item' => $this->user->name,
        ]);
    }
}
<?php

namespace Filament\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Filament\Contracts\User as UserContract;

class Users extends Component
{
    use AuthorizesRequests, WithPagination;

    public function render()
    {        
        $userClass = app(UserContract::class);
        $this->authorize('view', $userClass);

        return view('filament::livewire.users.index', [
            'users' => $userClass::paginate(12),
        ]);
    }
}
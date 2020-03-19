<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Filament\Contracts\User as UserContract;

class Users extends Component
{
    use WithPagination;

    public function render()
    {
        $userClass = app(UserContract::class);

        return view('filament::livewire.users', [
            'users' => $userClass::paginate(9),
        ]);
    }
}
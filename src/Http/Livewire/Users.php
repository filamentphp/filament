<?php

namespace Alpine\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Alpine\Contracts\User as UserContract;

class Users extends Component
{
    use WithPagination;

    public function render()
    {
        $userModel = app(UserContract::class);

        return view('alpine::livewire.users', [
            'users' => $userModel::paginate(2),
        ]);
    }
}
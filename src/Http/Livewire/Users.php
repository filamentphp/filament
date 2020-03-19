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
        $userModel = app(UserContract::class);

        return view('filament::livewire.users', [
            'users' => $userModel::paginate(2),
        ]);
    }
}
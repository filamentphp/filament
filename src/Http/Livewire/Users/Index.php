<?php

namespace Filament\Http\Livewire\Users;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('filament::livewire.users.index', [
            'users' => app('Filament\User')::paginate(12),
        ])
            ->layout('filament::layouts.app', ['title' => __('Users')]);
    }
}
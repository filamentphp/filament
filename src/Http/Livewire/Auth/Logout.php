<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public $class;
    
    public function logout()
    {
        if (Auth::guest()) {
            return redirect()->route('filament.login');
        }

        Auth::logout();
        session()->flash('message', __('filament::auth.loggedout'));
        return redirect()->route('filament.login');
    }

    public function render()
    {
        return view('filament::livewire.auth.logout', ['label' => __('filament::auth.logout')]);
    }
}
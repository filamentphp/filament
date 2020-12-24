<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public $class;
    
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        if (Auth::guest()) {
            return redirect()->route('filament.login');
        }

        Auth::logout();

        return redirect()->route('filament.login');
    }

    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.auth.logout', ['label' => __('filament::auth.logout')]);
    }
}
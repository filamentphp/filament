<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Action;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Action
{
    public $class;

    public function submit()
    {
        Auth::guard('filament')->logout();

        return redirect()->route('filament.auth.login');
    }

    public function render()
    {
        return view('filament::.auth.logout', ['label' => __('filament::auth.logout')]);
    }
}

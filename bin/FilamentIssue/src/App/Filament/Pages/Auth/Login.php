<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BasePage;

class Login extends BasePage
{
    public function mount(): void
    {
        parent::mount();

        if (app()->environment('local')) {
            $this->form->fill([
                'email' => 'test@filamentphp.com',
                'password' => 'password',
                'remember' => true,
            ]);
        }
    }
}

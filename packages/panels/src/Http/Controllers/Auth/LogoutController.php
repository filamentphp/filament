<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        $session = session()->all(); // We will get old session values.
        Filament::getCurrentPanel()->auth()->logout(); // We will log out only from current panel.

        session()->invalidate(); // it will invalidate and destroy our session data.
        session()->regenerateToken(); // it will regenerate session token.
        session($this->removeGuardVariables($session)); // it will remove guard values such as password_hash and login from old session data.

        return app(LogoutResponse::class); // it will send user to logout response.
    }

    public function removeGuardVariables(array $session): array
    {
        $guard = Filament::getCurrentPanel()->getAuthGuard(); // intended way to get guard name of the panel you've been logged in.
        foreach ($session as $sessionKey => $sessionValue) {
            if (str_starts_with($sessionKey, 'password_hash_' . $guard)
                || str_starts_with($sessionKey, 'login_' . $guard)) {
                unset($session[$sessionKey]); // if string starts with password_hash or login just remove it.
            }
        }

        return $session; // return it for freshly new created session.
    }
}

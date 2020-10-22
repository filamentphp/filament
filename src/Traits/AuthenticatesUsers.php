<?php

namespace Filament\Traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait AuthenticatesUsers
{
    /**
     * Get the login username to be used.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Get the translated label based on the username.
     * 
     * @return string
     */
    public function type()
    {
        return ($this->username() === 'email') ? 'email' : 'text';
    }

    /**
     * Get the translated label based on the username.
     * 
     * @return string
     */
    public function label()
    {
        $username = ($this->username() === 'email') ? 'filament::auth.username.email' : 'filament::auth.username.username';
        return trans($username);
    }
}
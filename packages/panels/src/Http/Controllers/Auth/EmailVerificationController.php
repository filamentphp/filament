<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\EmailVerificationResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EmailVerificationController
{
    public function __invoke(): EmailVerificationResponse
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        if ((! $user->hasVerifiedEmail()) && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return app(EmailVerificationResponse::class);
    }
}

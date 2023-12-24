<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Http\Responses\Auth\Contracts\EmailVerificationResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController
{
    public function __invoke(EmailVerificationRequest $request): EmailVerificationResponse
    {
        $request->fulfill();

        return app(EmailVerificationResponse::class);
    }
}

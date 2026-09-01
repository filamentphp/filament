<?php

namespace Filament\Auth\Http\Controllers;

use Filament\Auth\Http\Responses\Contracts\EmailVerificationResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController
{
    public function __invoke(EmailVerificationRequest $request): EmailVerificationResponse
    {
        $request->fulfill();

        return app(EmailVerificationResponse::class);
    }
}

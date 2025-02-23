<?php

namespace Filament\Auth\Http\Controllers;

use Filament\Auth\Http\Requests\EmailChangeVerificationRequest;
use Filament\Auth\Http\Responses\Contracts\EmailChangeVerificationResponse;
use Filament\Notifications\Notification;

class EmailChangeVerificationController
{
    public function __invoke(EmailChangeVerificationRequest $request): EmailChangeVerificationResponse
    {
        $request->fulfill();

        Notification::make()
            ->title(__('filament-panels::auth/http/controllers/email-change-verification-controller.notifications.verified.title'))
            ->body(__('filament-panels::auth/http/controllers/email-change-verification-controller.notifications.verified.body', [
                'email' => decrypt($request->route('email')),
            ]))
            ->success()
            ->send();

        return app(EmailChangeVerificationResponse::class);
    }
}

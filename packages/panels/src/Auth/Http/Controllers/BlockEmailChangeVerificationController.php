<?php

namespace Filament\Auth\Http\Controllers;

use Filament\Auth\Http\Requests\BlockEmailChangeVerificationRequest;
use Filament\Auth\Http\Responses\Contracts\BlockEmailChangeVerificationResponse;
use Filament\Notifications\Notification;

class BlockEmailChangeVerificationController
{
    public function __invoke(BlockEmailChangeVerificationRequest $request): BlockEmailChangeVerificationResponse
    {
        $isSuccessful = $request->fulfill();

        if ($isSuccessful) {
            Notification::make()
                ->title(__('filament-panels::auth/http/controllers/block-email-change-verification-controller.notifications.blocked.title'))
                ->body(__('filament-panels::auth/http/controllers/block-email-change-verification-controller.notifications.blocked.body', [
                    'email' => decrypt($request->route('email')),
                ]))
                ->success()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title(__('filament-panels::auth/http/controllers/block-email-change-verification-controller.notifications.failed.title'))
                ->body(__('filament-panels::auth/http/controllers/block-email-change-verification-controller.notifications.failed.body', [
                    'email' => decrypt($request->route('email')),
                ]))
                ->danger()
                ->persistent()
                ->send();
        }

        return app(BlockEmailChangeVerificationResponse::class);
    }
}

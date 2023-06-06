<div class="space-y-6">
    <p class="text-center text-sm text-gray-600 dark:text-gray-300">
        {{
            __('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_sent', [
                'email' => filament()->auth()->user()->getEmailForVerification(),
            ])
        }}
    </p>

    <p class="text-center text-sm text-gray-600 dark:text-gray-300">
        {{ __('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_not_received') }}

        {{ $this->resendNotificationAction }}
    </p>
</div>

<x-filament::layouts.card>
    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{
            __('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_sent', [
                'email' => filament()->auth()->user()->getEmailForVerification(),
            ])
        }}
    </p>

    <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_not_received') }}

        {{ $this->resendNotificationAction }}
    </p>
</x-filament::layouts.card>

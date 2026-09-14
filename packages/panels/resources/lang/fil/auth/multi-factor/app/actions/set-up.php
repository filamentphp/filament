<?php

return [
    'label' => 'I-set up',
    'modal' => [
        'heading' => 'I-set up ang authenticator app',
        'description' => 'Kakailanganin mo ng app tulad ng Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) para makumpleto ito.',
        'content' => [
            'qr_code' => [
                'instruction' => 'I-scan ang QR code na ito gamit ang authenticator app mo:',
                'alt' => 'QR code na i-scan gamit ang authenticator app',
            ],
            'text_code' => [
                'instruction' => 'O ilagay nang manual ang code na ito:',
                'messages' => [
                    'copied' => 'Nakopya',
                ],
            ],
            'recovery_codes' => [
                'instruction' => 'I-save ang mga sumusunod na recovery code sa ligtas na lugar. Isang beses lang ipapakita ang mga ito, pero kakailanganin mo ang mga ito kung mawalan ka ng access sa authenticator app mo:',
            ],
        ],
        'form' => [
            'code' => [
                'label' => 'Ilagay ang 6-digit code mula sa authenticator app',
                'validation_attribute' => 'verification code',
                'below_content' => 'Kakailanganin mong ilagay ang 6-digit code mula sa authenticator app tuwing magsa-sign in ka o gagawa ng sensitibong action.',
                'messages' => [
                    'invalid' => 'Hindi valid ang inilagay mong code.',
                    'rate_limited' => 'Masyadong maraming attempt. Subukan ulit mamaya.',
                ],
            ],
        ],
        'actions' => [
            'submit' => [
                'label' => 'I-enable ang authenticator app',
            ],
        ],
    ],
    'notifications' => [
        'enabled' => [
            'title' => 'Na-enable na ang authenticator app',
        ],
    ],
];

<?php

return [
    'label' => 'I-set up',
    'modal' => [
        'heading' => 'I-set up ang mga email verification code',
        'description' => 'Kakailanganin mong ilagay ang 6-digit code na ipapadala namin sa email mo tuwing magsa-sign in ka o gagawa ng sensitibong action. Tingnan ang email mo para sa 6-digit code upang makumpleto ang setup.',
        'form' => [
            'code' => [
                'label' => 'Ilagay ang 6-digit code na ipinadala namin sa email mo',
                'validation_attribute' => 'verification code',
                'actions' => [
                    'resend' => [
                        'label' => 'Magpadala ng bagong code sa email',
                        'notifications' => [
                            'resent' => [
                                'title' => 'Nagpadala kami ng bagong code sa email mo',
                            ],
                            'throttled' => [
                                'title' => 'Masyadong maraming resend attempt. Maghintay bago humiling ng panibagong code.',
                            ],
                        ],
                    ],
                ],
                'messages' => [
                    'invalid' => 'Hindi valid ang inilagay mong code.',
                    'rate_limited' => 'Masyadong maraming attempt. Subukan ulit mamaya.',
                ],
            ],
        ],
        'actions' => [
            'submit' => [
                'label' => 'I-enable ang mga email verification code',
            ],
        ],
    ],
    'notifications' => [
        'enabled' => [
            'title' => 'Na-enable na ang mga email verification code',
        ],
    ],
];

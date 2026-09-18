<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'Mga email verification code',
            'below_content' => 'Tumanggap ng pansamantalang code sa email address mo para i-verify ang pagkakakilanlan mo habang nagla-login.',
            'messages' => [
                'enabled' => 'Naka-enable',
                'disabled' => 'Naka-disable',
            ],
        ],
    ],
    'login_form' => [
        'label' => 'Magpadala ng code sa email mo',
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
            ],
        ],
    ],
];

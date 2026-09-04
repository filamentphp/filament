<?php

return [
    'management_schema' => [
        'actions' => [
            'label' => 'App na authenticator',
            'below_content' => 'Gumamit ng secure na app para gumawa ng pansamantalang code para sa login verification.',
            'messages' => [
                'enabled' => 'Naka-enable',
                'disabled' => 'Naka-disable',
            ],
        ],
    ],
    'login_form' => [
        'label' => 'Gumamit ng code mula sa authenticator app mo',
        'code' => [
            'label' => 'Ilagay ang 6-digit code mula sa authenticator app',
            'validation_attribute' => 'verification code',
            'actions' => [
                'use_recovery_code' => [
                    'label' => 'Gumamit na lang ng recovery code',
                ],
            ],
            'messages' => [
                'invalid' => 'Hindi valid ang inilagay mong code.',
            ],
        ],
        'recovery_code' => [
            'label' => 'O, maglagay ng recovery code',
            'validation_attribute' => 'code para sa recovery',
            'messages' => [
                'invalid' => 'Hindi valid ang inilagay mong recovery code.',
            ],
        ],
    ],
];

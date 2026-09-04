<?php

return [
    'label' => 'I-off',
    'modal' => [
        'heading' => 'I-disable ang authenticator app',
        'description' => 'Sigurado ka bang gusto mong ihinto ang paggamit ng authenticator app? Kapag na-disable ito, mawawala ang dagdag na proteksiyon sa account mo.',
        'form' => [
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
                    'rate_limited' => 'Masyadong maraming attempt. Subukan ulit mamaya.',
                ],
            ],
            'recovery_code' => [
                'label' => 'O, maglagay ng recovery code',
                'validation_attribute' => 'code para sa recovery',
                'messages' => [
                    'invalid' => 'Hindi valid ang inilagay mong recovery code.',
                    'rate_limited' => 'Masyadong maraming attempt. Subukan ulit mamaya.',
                ],
            ],
        ],
        'actions' => [
            'submit' => [
                'label' => 'I-disable ang authenticator app',
            ],
        ],
    ],
    'notifications' => [
        'disabled' => [
            'title' => 'Na-disable na ang authenticator app',
        ],
    ],
];

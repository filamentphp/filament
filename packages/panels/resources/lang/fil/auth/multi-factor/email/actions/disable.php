<?php

return [
    'label' => 'I-off',
    'modal' => [
        'heading' => 'I-disable ang mga email verification code',
        'description' => 'Sigurado ka bang gusto mong ihinto ang pagtanggap ng mga email verification code? Kapag na-disable ito, mawawala ang dagdag na proteksiyon sa account mo.',
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
                'label' => 'I-disable ang mga email verification code',
            ],
        ],
    ],
    'notifications' => [
        'disabled' => [
            'title' => 'Na-disable na ang mga email verification code',
        ],
    ],
];

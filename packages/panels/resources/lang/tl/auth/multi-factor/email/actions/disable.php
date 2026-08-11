<?php

return [

    'label' => 'I-off',

    'modal' => [

        'heading' => 'I-disable ang mga email verification code',

        'description' => 'Sigurado ka bang gusto mong ihinto ang pagtanggap ng mga email verification code? Kapag na-disable ito, mawawala ang dagdag na layer ng seguridad sa iyong account.',

        'form' => [

            'code' => [

                'label' => 'Ilagay ang 6-digit code na ipinadala namin sa email mo',

                'actions' => [

                    'resend' => [

                        'label' => 'Magpadala ng bagong code sa email',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Nagpadala kami ng bagong code sa email mo',
                            ],

                            'throttled' => [
                                'title' => 'Masyadong maraming subok na magpadala ulit. Maghintay muna bago humingi ng panibagong code.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Invalid ang code na inilagay mo.',

                    'rate_limited' => 'Masyadong maraming subok. Subukan ulit mamaya.',

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
            'title' => 'Na-disable ang mga email verification code',
        ],

    ],

];

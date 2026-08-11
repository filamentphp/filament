<?php

return [

    'label' => 'I-set up',

    'modal' => [

        'heading' => 'I-set up ang mga email verification code',

        'description' => 'Kakailanganin mong ilagay ang 6-digit code na ipinapadala namin sa email mo sa tuwing magsa-sign in ka o gagawa ng sensitibong action. Tingnan ang iyong email para sa 6-digit code at makumpleto ang setup.',

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
                'label' => 'I-enable ang mga email verification code',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Na-enable ang mga email verification code',
        ],

    ],

];

<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Mga email verification code',

            'below_content' => 'Tumanggap ng temporary code sa iyong email address para i-verify ang identity mo habang nagla-login.',

            'messages' => [
                'enabled' => 'Naka-enable',
                'disabled' => 'Naka-disable',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Magpadala ng code sa iyong email',

        'code' => [

            'label' => 'Ilagay ang 6-digit code na ipinadala namin sa email mo',

            'validation_attribute' => 'code',

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

            ],

        ],

    ],

];

<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator app',

            'below_content' => 'Gumamit ng secure na app para gumawa ng temporary code para sa login verification.',

            'messages' => [
                'enabled' => 'Naka-enable',
                'disabled' => 'Naka-disable',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gumamit ng code mula sa iyong authenticator app',

        'code' => [

            'label' => 'Ilagay ang 6-digit code mula sa authenticator app',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Gumamit na lang ng recovery code',
                ],

            ],

            'messages' => [

                'invalid' => 'Invalid ang code na inilagay mo.',

            ],

        ],

        'recovery_code' => [

            'label' => 'O, maglagay ng recovery code',

            'validation_attribute' => 'recovery code',

            'messages' => [

                'invalid' => 'Invalid ang recovery code na inilagay mo.',

            ],

        ],

    ],

];

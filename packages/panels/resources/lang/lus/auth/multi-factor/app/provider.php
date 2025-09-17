<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator app',

            'below_content' => 'Login verification atan secure app in temporary code a siam hmang rawh.',

            'messages' => [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'I authenticator app ami code hmang rawh',

        'code' => [

            'label' => '6-digit code authenticator app ami enter rawh',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Recovery code hmang zawk rawh',
                ],

            ],

            'messages' => [

                'invalid' => 'Hemi code hi a diklo.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Or, recovery code enter rawh',

            'validation_attribute' => 'recovery code',

            'messages' => [

                'invalid' => 'Hemi recovery code hi a diklo.',

            ],

        ],

    ],

];

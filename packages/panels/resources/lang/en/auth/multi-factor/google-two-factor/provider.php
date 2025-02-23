<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator app',

            'below_content' => 'Use a secure app to generate a temporary code for login verification.',

            'messages' => [
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Use a code from your authenticator app',

        'code' => [

            'label' => 'Enter the 6-digit code from the authenticator app',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Use a recovery code instead',
                ],

            ],

            'messages' => [

                'invalid' => 'The code you entered is invalid.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Or, enter a recovery code',

            'validation_attribute' => 'recovery code',

            'messages' => [

                'invalid' => 'The recovery code you entered is invalid.',

            ],

        ],

    ],

];

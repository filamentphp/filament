<?php

return [

    'label' => 'Turn off',

    'modal' => [

        'heading' => 'Disable authenticator app',

        'description' => 'Are you sure you want to stop using the authenticator app? Disabling this will remove an extra layer of security from your account.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Disable authenticator app',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Authenticator app has been disabled',
        ],

    ],

];

<?php

return [

    'label' => 'Regenerate recovery codes',

    'modal' => [

        'heading' => 'Regenerate authenticator app recovery codes',

        'description' => 'If you lose your recovery codes, you can regenerate them here. Your old recovery codes will be invalidated immediately.',

        'form' => [

            'code' => [

                'label' => 'Enter the 6-digit code from the authenticator app',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'The code you entered is invalid.',

                ],

            ],

            'password' => [

                'label' => 'Or, enter your current password',

                'validation_attribute' => 'password',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerate recovery codes',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'New authenticator app recovery codes have been generated',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'New recovery codes',

            'description' => 'Please save the following recovery codes in a safe place. They will only be shown once, but you\'ll need them if you lose access to your authenticator app:',

            'actions' => [

                'submit' => [
                    'label' => 'Close',
                ],

            ],

        ],

    ],

];

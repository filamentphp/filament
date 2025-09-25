<?php

return [

    'label' => 'Turn off',

    'modal' => [

        'heading' => 'Disable authenticator app',

        'description' => 'Authenticator app hman hi tihtawp i duh tak tak em? Hemi tihtawp hian security dang I account a a pek belh ho a paih dawn ani.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Disable authenticator app',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Authenticator app disabled ani',
        ],

    ],

];

<?php

return [

    'label' => 'I-off',

    'modal' => [

        'heading' => 'I-disable ang authenticator app',

        'description' => 'Sigurado ka bang gusto mong ihinto ang paggamit ng authenticator app? Kapag na-disable ito, mawawala ang dagdag na layer ng seguridad sa iyong account.',

        'form' => [

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

                    'rate_limited' => 'Masyadong maraming subok. Subukan ulit mamaya.',

                ],

            ],

            'recovery_code' => [

                'label' => 'O, maglagay ng recovery code',

                'validation_attribute' => 'recovery code',

                'messages' => [

                    'invalid' => 'Invalid ang recovery code na inilagay mo.',

                    'rate_limited' => 'Masyadong maraming subok. Subukan ulit mamaya.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'I-disable ang authenticator app',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Na-disable ang authenticator app',
        ],

    ],

];

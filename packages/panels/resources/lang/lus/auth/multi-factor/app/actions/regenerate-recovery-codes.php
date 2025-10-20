<?php

return [

    'label' => 'Regenerate recovery codes',

    'modal' => [

        'heading' => 'Authenticator app a recovery codes siamthar na',

        'description' => 'I recovery codes iti bo  anih chuan, heta tang hian a thar I siam thei ang. A code hlui ho kha chu an hman theih tawhloh nghal ang.',

        'form' => [

            'code' => [

                'label' => '6-digit code authenticator app ami enter rawh',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'Hemi code hi a diklo.',

                ],

            ],

            'password' => [

                'label' => 'Or, tun a I password enter rawh',

                'validation_attribute' => 'password',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Recovery codes thar',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Authenticator app a recovery codes thar tur siam a ni',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Recovery codes thar',

            'description' => 'Khawngaihin heng recovery codes ho hi him takin dahtha ang che. Vawikhat chiah an rawn lang dawn a, mahse I authenticator app a access I hloh hunah I mamawh ang:',

            'actions' => [

                'submit' => [
                    'label' => 'Close',
                ],

            ],

        ],

    ],

];

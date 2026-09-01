<?php

return [

    'label' => 'Taasta taastamiskoodid',

    'modal' => [

        'heading' => 'Taasta autentimise rakenduse taastamiskoodid',

        'description' => 'Kui kaotate oma taastamiskoodid, saate neid siin taastada. Teie vanad taastamiskoodid tühistatakse kohe.',

        'form' => [

            'code' => [

                'label' => 'Sisestage autentimise rakendusest saadud 6-kohaline kood',

                'validation_attribute' => 'kood',

                'messages' => [

                    'invalid' => 'Sisestatud kood on vale.',

                    'rate_limited' => 'Liiga palju katseid. Palun proovige hiljem uuesti.',

                ],

            ],

            'password' => [

                'label' => 'Või sisestage oma praegune parool',

                'validation_attribute' => 'parool',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Taasta taastamiskoodid',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Uued autentimise rakenduse taastamiskoodid on loodud',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Uued taastamiskoodid',

            'description' => 'Palun salvestage järgmised taastamiskoodid turvalisse kohta. Need kuvatakse ainult üks kord, kuid vajate neid, kui kaotate juurdepääsu oma autentimise rakendusele:',

            'actions' => [

                'submit' => [
                    'label' => 'Sulge',
                ],

            ],

        ],

    ],

];

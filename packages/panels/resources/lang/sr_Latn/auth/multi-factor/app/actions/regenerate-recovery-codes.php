<?php

return [

    'label' => 'Ponovo generišite kodove za oporavak',

    'modal' => [

        'heading' => 'Ponovo generišite kodove za oporavak aplikacije za autentifikaciju',

        'description' => 'Ako izgubite kodove za oporavak, može da ih ponovo generišite ovde. Nakon ove akcije vaši stari kodovi za oporavak biće nevažeći.',

        'form' => [

            'code' => [

                'label' => 'Unesite kod od 6 cifara iz aplikacije za autentifikaciju',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Kod koji ste uneli nije ispravan.',

                ],

            ],

            'password' => [

                'label' => 'Ili unesite trenutnu lozinku',

                'validation_attribute' => 'lozinka',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ponovo generišite kodove za oporavak',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Novi kodovi za oporavak aplikacije za autentifikaciju su generisani',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Novi kodovi za oporavak',

            'description' => 'Čuvajte ove kodove za oporavak na bezbednom mestu. Oni će biti prikazani samo jednom, ali će biti neophodni ako izgubite pristup aplikaciji za autentifikaciju.',

            'actions' => [

                'submit' => [
                    'label' => 'Zatvori',
                ],

            ],

        ],

    ],

];

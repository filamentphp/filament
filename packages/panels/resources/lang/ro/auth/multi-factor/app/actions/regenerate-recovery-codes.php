<?php

return [

    'label' => 'Regenerează codurile de recuperare',

    'modal' => [

        'heading' => 'Regenerare coduri de recuperare pentru aplicația de autentificare',

        'description' => 'Dacă ați pierdut codurile de recuperare, le puteți regenera aici. Codurile de recuperare vechi vor fi invalidate imediat.',

        'form' => [

            'code' => [

                'label' => 'Introduceți codul din 6 cifre din aplicația de autentificare',

                'validation_attribute' => 'cod',

                'messages' => [

                    'invalid' => 'Codul introdus este invalid.',

                ],

            ],

            'password' => [

                'label' => 'Sau, introduceți parola curentă',

                'validation_attribute' => 'parolă',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerează codurile de recuperare',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Noi coduri de recuperare au fost generate',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Coduri de recuperare noi',

            'description' => 'Vă rugăm să salvați următoarele coduri de recuperare într-un loc sigur. Acestea vor fi afișate doar o singură dată, dar veți avea nevoie de ele dacă pierdeți accesul la aplicația de autentificare:',

            'actions' => [

                'submit' => [
                    'label' => 'Închide',
                ],

            ],

        ],

    ],

];

<?php

return [

    'label' => 'Dezactivează',

    'modal' => [

        'heading' => 'Dezactivare aplicație de autentificare',

        'description' => 'Sigur doriți să nu mai folosiți aplicația de autentificare? Dezactivarea acesteia va elimina un nivel suplimentar de securitate din contul dumneavoastră.',

        'form' => [

            'code' => [

                'label' => 'Introduceți codul din 6 cifre din aplicația de autentificare',

                'validation_attribute' => 'cod',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Folosește în schimb un cod de recuperare',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Codul introdus este invalid.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Sau, introduceți un cod de recuperare',

                'validation_attribute' => 'cod de recuperare',

                'messages' => [

                    'invalid' => 'Codul de recuperare introdus este invalid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Dezactivează aplicația de autentificare',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplicația de autentificare a fost dezactivată',
        ],

    ],

];

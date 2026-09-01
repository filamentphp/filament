<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplicație de autentificare',

            'below_content' => 'Folosiți o aplicație securizată pentru a genera un cod temporar pentru verificarea autentificării.',

            'messages' => [
                'enabled' => 'Activat',
                'disabled' => 'Dezactivat',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Folosește un cod din aplicația de autentificare',

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

];

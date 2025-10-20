<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplikacija za autentifikaciju',

            'below_content' => 'Koristite sigurnosnu aplikaciju za generisanje privremenog koda za verifikaciju prijave.',

            'messages' => [
                'enabled' => 'Uključeno',
                'disabled' => 'Isključeno',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Unesite kod iz aplikacije za autentifikaciju',

        'code' => [

            'label' => 'Unesite kod od 6 cifara iz aplikacije za autentifikaciju',

            'validation_attribute' => 'kod',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Koristite kod za oporavak',
                ],

            ],

            'messages' => [

                'invalid' => 'Kod koji ste uneli nije ispravan.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Ili unesite kod za oporavak',

            'validation_attribute' => 'kod za oporavak',

            'messages' => [

                'invalid' => 'Kod za oporavak koji ste uneli nije ispravan.',

            ],

        ],

    ],

];

<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentiseringsapp',

            'below_content' => 'Bruk en sikker app for å generere en midlertidig kode for innloggingsbekreftelse.',

            'messages' => [
                'enabled' => 'Aktivert',
                'disabled' => 'Deaktivert',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Bruk en kode fra autentiseringsappen din',

        'code' => [

            'label' => 'Skriv inn 6-sifret kode fra autentiseringsappen',

            'validation_attribute' => 'kode',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Bruk en gjenopprettingskode i stedet',
                ],

            ],

            'messages' => [

                'invalid' => 'Koden du skrev inn er ugyldig.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Eller, skriv inn en gjenopprettingskode',

            'validation_attribute' => 'gjenopprettingskode',

            'messages' => [

                'invalid' => 'Gjenopprettingskoden du skrev inn er ugyldig.',

            ],

        ],

    ],

];

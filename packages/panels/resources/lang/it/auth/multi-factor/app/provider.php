<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'App di autenticazione',

            'below_content' => 'Utilizza un\'app sicura per generare un codice temporaneo per la verifica dell\'accesso.',

            'messages' => [
                'enabled' => 'Abilitato',
                'disabled' => 'Disabilitato',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Usa un codice dalla tua app di autenticazione',

        'code' => [

            'label' => 'Inserisci il codice di 6 cifre dall\'app di autenticazione',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Utilizza un codice di recupero',
                ],

            ],

            'messages' => [

                'invalid' => 'Il codice inserito non è valido.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Oppure, inserisci un codice di recupero',

            'validation_attribute' => 'recovery code',

            'messages' => [

                'invalid' => 'Il codice di recupero inserito non è valido.',

            ],

        ],

    ],

];

<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Codici di verifica email',

            'below_content' => 'Ricevi un codice temporaneo al tuo indirizzo email per verificare la tua identità durante il login.',

            'messages' => [
                'enabled' => 'Abilitato',
                'disabled' => 'Disabilitato',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Invia un codice alla tua email',

        'code' => [

            'label' => 'Inserisci il codice di 6 cifre che ti abbiamo inviato via email',

            'validation_attribute' => 'code',

            'actions' => [

                'resend' => [

                    'label' => 'Invia un nuovo codice via email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Ti abbiamo inviato un nuovo codice via email',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Il codice inserito non è valido.',

            ],

        ],

    ],

];

<?php

return [

    'label' => 'Configura',

    'modal' => [

        'heading' => 'Configura i codici di verifica email',

        'description' => 'Dovrai inserire il codice di 6 cifre che ti inviamo via email ogni volta che accedi o esegui azioni sensibili. Controlla la tua email per un codice di 6 cifre per completare la configurazione.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Abilita i codici di verifica email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'I codici di verifica email sono stati abilitati',
        ],

    ],

];

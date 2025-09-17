<?php

return [

    'label' => 'Disabilita',

    'modal' => [

        'heading' => 'Disabilita i codici di verifica email',

        'description' => 'Sei sicuro di voler smettere di ricevere codici di verifica email? Disabilitare questa opzione rimuoverà un ulteriore livello di sicurezza dal tuo account.',

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
                'label' => 'Disabilita i codici di verifica email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'I codici di verifica email sono stati disabilitati',
        ],

    ],

];

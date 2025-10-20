<?php

return [

    'label' => 'Rigenera i codici di recupero',

    'modal' => [

        'heading' => 'Rigenera i codici di recupero dell\'app di autenticazione',

        'description' => 'Se perdi i tuoi codici di recupero, puoi rigenerarli qui. I tuoi vecchi codici di recupero saranno invalidati immediatamente.',

        'form' => [

            'code' => [

                'label' => 'Inserisci il codice di 6 cifre dall\'app di autenticazione',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'Il codice inserito non è valido.',

                ],

            ],

            'password' => [

                'label' => 'Oppure, inserisci la tua password attuale',

                'validation_attribute' => 'password',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Rigenera i codici di recupero',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Sono stati rigenerati nuovi codici di recupero',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nuovi codici di recupero',

            'description' => 'Si prega di salvare i seguenti codici di recupero in un luogo sicuro. Saranno mostrati solo una volta, ma ne avrai bisogno se perdi l\'accesso alla tua app di autenticazione:',

            'actions' => [

                'submit' => [
                    'label' => 'Chiudi',
                ],

            ],

        ],

    ],

];

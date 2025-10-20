<?php

return [

    'label' => 'Disabilita',

    'modal' => [

        'heading' => 'Disabilita l\'app di autenticazione',

        'description' => 'Sei sicuro di voler smettere di utilizzare l\'app di autenticazione? Disabilitare questa opzione rimuoverà un ulteriore livello di sicurezza dal tuo account.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Disabilita l\'app di autenticazione',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'L\'app di autenticazione è stata disabilitata',
        ],

    ],

];

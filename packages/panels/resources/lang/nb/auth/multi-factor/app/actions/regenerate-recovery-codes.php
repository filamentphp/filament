<?php

return [

    'label' => 'Regenerer gjenopprettingskoder',

    'modal' => [

        'heading' => 'Regenerer gjenopprettingskoder for autentiseringsapp',

        'description' => 'Hvis du mister gjenopprettingskodene dine, kan du regenerere dem her. Dine gamle gjenopprettingskoder blir ugyldige umiddelbart.',

        'form' => [

            'code' => [

                'label' => 'Skriv inn 6-sifret kode fra autentiseringsappen',

                'validation_attribute' => 'kode',

                'messages' => [

                    'invalid' => 'Koden du skrev inn er ugyldig.',

                ],

            ],

            'password' => [

                'label' => 'Eller, skriv inn ditt nåværende passord',

                'validation_attribute' => 'passord',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Regenerer gjenopprettingskoder',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nye gjenopprettingskoder for autentiseringsapp er generert',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nye gjenopprettingskoder',

            'description' => 'Vennligst lagre følgende gjenopprettingskoder på et trygt sted. De vil bare vises én gang, men du trenger dem hvis du mister tilgang til autentiseringsappen:',

            'actions' => [

                'submit' => [
                    'label' => 'Lukk',
                ],

            ],

        ],

    ],

];

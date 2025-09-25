<?php

return [

    'label' => 'Slå av',

    'modal' => [

        'heading' => 'Deaktiver autentiseringsapp',

        'description' => 'Er du sikker på at du vil slutte å bruke autentiseringsappen? Å deaktivere dette vil fjerne et ekstra sikkerhetslag fra kontoen din.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Deaktiver autentiseringsapp',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentiseringsapp er deaktivert',
        ],

    ],

];

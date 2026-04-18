<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar codis de verificació per correu electrònic',

        'description' => 'Necessitaràs introduïr el codi de 6 dígits que t\'enviem per correu electrònic cada cop que iniciïs sessió o realitzis accions sensibles. Revisa el correu per trobar el codi de 6 dígits i completar la configuració.',

        'form' => [

            'code' => [

                'label' => 'Introdueix el codi de 6 dígits que t\'enviem per correu electrònic',

                'validation_attribute' => 'codi',

                'actions' => [

                    'resend' => [

                        'label' => 'Enviar un codi nou per correu electrònic',

                        'notifications' => [

                            'resent' => [
                                'title' => 'T\'hem enviat un codi nou per correu electrònic.',
                            ],

                            'throttled' => [
                                'title' => 'Massa intents de reenviament. Espera abans de sol·licitar un altre codi.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'El codi introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Si us plau, intenta-ho més tard.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Habilitar codis de verificació per correu electrònic',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'S\'han activat els codis de verificació per correu electrònic',
        ],

    ],

];

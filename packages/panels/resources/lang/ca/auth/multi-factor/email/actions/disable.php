<?php

return [

    'label' => 'Apagar',

    'modal' => [

        'heading' => 'Desactivar codis de verificació per correu',

        'description' => 'Segur que vols deixar de rebre codis de verificació per correu? Desactivar aquesta opció eliminarà una capa addicional de seguretat del teu compte.',

        'form' => [

            'code' => [

                'label' => 'Ingressa el codi de 6 dígits que t\'enviem per correu electrònic',

                'validation_attribute' => 'codi',

                'actions' => [

                    'resend' => [

                        'label' => 'Enviar un codi nou per correu electrònic',

                        'notifications' => [

                            'resent' => [
                                'title' => 'T\'hem enviat un codi nou per correu electrònic.',
                            ],

                            'throttled' => [
                                'title' => 'Massa intents de reenviament. Espera abans de sol·licitar-ne un altre.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'El codi introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Intenta-ho més tard.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Desactivar codis de verificació per correu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Els codis de verificació per correu han estat desactivats',
        ],

    ],

];

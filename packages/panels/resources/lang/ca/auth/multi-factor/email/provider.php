<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Codis de verificació per correu electrònic',

            'below_content' => 'Rep un codi temporal al teu correu electrònic per verificar la teva identitat durant l\'inici de sessió.',

            'messages' => [
                'enabled' => 'Activats',
                'disabled' => 'Desactivats',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Enviar un codi al teu correu electrònic',

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

            ],

        ],

    ],

];

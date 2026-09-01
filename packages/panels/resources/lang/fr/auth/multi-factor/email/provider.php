<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Codes de vérification par email',

            'below_content' => 'Recevez un code temporaire à votre adresse email pour vérifier votre identité lors de la connexion.',

            'messages' => [
                'enabled' => 'Activé',
                'disabled' => 'Désactivé',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Envoyer un code à votre email',

        'code' => [

            'label' => 'Entrez le code à 6 chiffres que nous vous avons envoyé par email',

            'validation_attribute' => 'code',

            'actions' => [

                'resend' => [

                    'label' => 'Envoyer un nouveau code par email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Nous vous avons envoyé un nouveau code par email',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Le code que vous avez entré est invalide.',

            ],

        ],

    ],

];

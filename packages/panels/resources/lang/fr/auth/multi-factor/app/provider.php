<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Application d\'authentification',

            'below_content' => 'Utilisez une application sécurisée pour générer un code temporaire pour la vérification de la connexion.',

            'messages' => [
                'enabled' => 'Activé',
                'disabled' => 'Désactivé',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Utilisez un code de votre application d\'authentification',

        'code' => [

            'label' => 'Entrez le code à 6 chiffres de l\'application d\'authentification',

            'validation_attribute' => 'code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Utiliser un code de récupération à la place',
                ],

            ],

            'messages' => [

                'invalid' => 'Le code que vous avez entré est invalide.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Ou, entrez un code de récupération',

            'validation_attribute' => 'code de récupération',

            'messages' => [

                'invalid' => 'Le code de récupération que vous avez entré est invalide.',

            ],

        ],

    ],

];

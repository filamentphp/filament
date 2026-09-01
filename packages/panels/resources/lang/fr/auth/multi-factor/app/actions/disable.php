<?php

return [

    'label' => 'Désactiver',

    'modal' => [

        'heading' => 'Désactiver l\'application d\'authentification',

        'description' => 'Êtes-vous sûr de vouloir arrêter d\'utiliser l\'application d\'authentification ? Désactiver cela supprimera une couche de sécurité supplémentaire de votre compte.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Désactiver l\'application d\'authentification',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'L\'application d\'authentification a été désactivée',
        ],

    ],

];

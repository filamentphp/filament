<?php

return [

    'label' => 'Régénérer les codes de récupération',

    'modal' => [

        'heading' => 'Régénérer les codes de récupération de l\'application d\'authentification',

        'description' => 'Si vous perdez vos codes de récupération, vous pouvez les régénérer ici. Vos anciens codes de récupération seront invalidés immédiatement.',

        'form' => [

            'code' => [

                'label' => 'Entrez le code à 6 chiffres de l\'application d\'authentification',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'Le code que vous avez entré est invalide.',

                ],

            ],

            'password' => [

                'label' => 'Ou, entrez votre mot de passe actuel',

                'validation_attribute' => 'mot de passe',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Régénérer les codes de récupération',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'De nouveaux codes de récupération de l\'application d\'authentification ont été générés',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nouveaux codes de récupération',

            'description' => 'Veuillez enregistrer les codes de récupération suivants dans un endroit sécurisé. Ils ne seront affichés qu\'une fois, mais vous en aurez besoin si vous perdez l\'accès à votre application d\'authentification :',

            'actions' => [

                'submit' => [
                    'label' => 'Fermer',
                ],

            ],

        ],

    ],

];

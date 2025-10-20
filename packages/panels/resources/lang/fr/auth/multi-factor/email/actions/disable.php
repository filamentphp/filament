<?php

return [

    'label' => 'Désactiver',

    'modal' => [

        'heading' => 'Désactiver les codes de vérification par email',

        'description' => 'Êtes-vous sûr de vouloir arrêter de recevoir les codes de vérification par email ? Désactiver cela supprimera une couche de sécurité supplémentaire de votre compte.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Désactiver les codes de vérification par email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Les codes de vérification par email ont été désactivés',
        ],

    ],

];

<?php

return [

    'label' => 'Configurer',

    'modal' => [

        'heading' => 'Configurer les codes de vérification par email',

        'description' => 'Vous devrez entrer le code à 6 chiffres que nous vous avons envoyé par email à chaque fois que vous vous connecterez ou effectuerez des actions sensibles. Vérifiez votre email pour un code à 6 chiffres pour terminer la configuration.',

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
                'label' => 'Activer les codes de vérification par email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Les codes de vérification par email ont été activés',
        ],

    ],

];

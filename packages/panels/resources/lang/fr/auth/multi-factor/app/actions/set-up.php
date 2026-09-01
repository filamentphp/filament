<?php

return [

    'label' => 'Configurer',

    'modal' => [

        'heading' => 'Configurer l\'application d\'authentification',

        'description' => <<<'BLADE'
            Vous aurez besoin d\'une application comme Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) pour compléter cette procédure.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scannez ce QR code avec votre application d\'authentification :',

                'alt' => 'QR code à scanner avec une application d\'authentification',

            ],

            'text_code' => [

                'instruction' => 'Ou entrez ce code manuellement :',

                'messages' => [
                    'copied' => 'Copié',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Veuillez enregistrer les codes de récupération suivants dans un endroit sécurisé. Ils ne seront affichés qu\'une fois, mais vous en aurez besoin si vous perdez l\'accès à votre application d\'authentification :',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Entrez le code à 6 chiffres de l\'application d\'authentification',

                'validation_attribute' => 'code',

                'below_content' => 'Vous devrez entrer le code à 6 chiffres de votre application d\'authentification à chaque fois que vous vous connecterez ou effectuerez des actions sensibles.',

                'messages' => [

                    'invalid' => 'Le code que vous avez entré est invalide.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Activer l\'application d\'authentification',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'L\'application d\'authentification a été activée',
        ],

    ],

];
